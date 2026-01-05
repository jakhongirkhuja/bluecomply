<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Http\Requests\Company\StoreIncidentBasicRequest;
use App\Http\Requests\Company\StoreIncidentCitationRequest;
use App\Http\Requests\Company\StoreIncidentDetailRequest;
use App\Http\Requests\Company\StoreIncidentFilesRequest;
use App\Http\Requests\Company\UpdateIncidentFileEditRequest;
use App\Http\Requests\Company\UpdateOtherIncidentRequest;
use App\Models\Company\Incident;
use App\Models\Company\IncidentFile;
use App\Models\Driver\Driver;
use App\Services\Company\IncidentService;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;

class IncidentController extends Controller
{
    public function __construct(protected IncidentService $service)
    {
    }

    public function show($id)
    {
        return response()->success(
            Incident::with('driver')
                ->where('id', $id)
                ->firstOrFail()
        );
    }

    public function store(StoreIncidentBasicRequest $request)
    {

        $data = $request->validated();
        $driverExist = Driver::where('id', $data['driver_id'])->whereHas('company', fn($q) => $q->where('user_id', auth()->id()))->firstOrFail();
        if ($driverExist) {
            $data['company_id'] = $driverExist->company_id;
            return $this->safe(fn() => response()->success(
                $this->service->store($data),
                Response::HTTP_CREATED)
            );
        }
        return response()->error('driver does not exist', Response::HTTP_NOT_FOUND);

    }

    public function createOtherIncidents(UpdateOtherIncidentRequest $request, Incident $incident)
    {
        $data = $request->validated();
        return $this->safe(function () use ($incident, $data) {

            if ($incident->type == 'other_incidents' && $incident->driver_id == $data['driver_id']) {

                $incident->update($data);
            }
            return response()->success($incident, Response::HTTP_OK);
        });
    }
    public function createCitation(StoreIncidentCitationRequest $request, Incident $incident)
    {
        $data = $request->validated();
        return $this->safe(function () use ($incident, $data) {

            if ($incident->type == 'citations' && $incident->driver_id == $data['driver_id']) {

                $incident->update($data);
            }
            return response()->success($incident, Response::HTTP_OK);
        });
    }
    protected function safe(callable $callback)
    {
        try {
            return $callback();
        } catch (\Throwable $e) {
            report($e);

            return response()->error(
                $e->getMessage(),
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    public function update(StoreIncidentDetailRequest $request, Incident $incident)
    {
        $data = $request->validated();
        Driver::where('id', $data['driver_id'])->whereHas('company', fn($q) => $q->where('user_id', auth()->id()))->firstOrFail();
        if ($incident->type == 'other_damage') {
            $validate = $request->validate([
                'damage_category_id' => 'required|numeric|exists:damage_categories,id',
                'third_party_required' => 'required|numeric|between:0,1',
                'third_party_name' => 'required_if:third_party_required,1|string|max:255',
                'third_party_contact' => 'required_if:third_party_required,1|string|max:255',
                'third_party_notes' => 'required_if:third_party_required,1|string',
            ]);
        }
        return $this->safe(fn() => response()->success($this->service->update($incident, $data)));

    }

    public function files(StoreIncidentFilesRequest $request, Incident $incident)
    {
        return $this->safe(fn() => response()->success($this->service->addfiles($request->validated(), $incident)));
    }

    public function fileNameEdit(UpdateIncidentFileEditRequest $request, Incident $incident, $id)
    {
        return $this->safe(function () use ($request, $id, $incident) {
            $s = IncidentFile::where('id', $id)->where('incident_id', $incident->id)->firstOrFail();
            $s->update(['file_name' => $request->input('name')]);
            return response()->success($s);
        });
    }

    public function filesDelete(Incident $incident, $id)
    {
        return $this->safe(function () use ($id, $incident) {
            $incidentFile = IncidentFile::where('id', $id)->where('incident_id', $incident->id)->firstOrFail();
            if ($incidentFile->file_name && Storage::disk('public')->exists($incidentFile->file_name)) {
                Storage::disk('public')->delete($incidentFile->file_name);
            }
            return response()->success($incidentFile->delete(), Response::HTTP_NO_CONTENT);
        });
    }

    public function destroy(Incident $incident)
    {
        return $this->safe(fn() => tap($incident, fn() => $incident->delete()));
    }
}
