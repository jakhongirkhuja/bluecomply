<?php

namespace App\Services\Company;

use App\Models\Company\Claim;
use App\Models\Company\Company;
use App\Models\Company\Document;
use App\Models\Company\DocumentType;
use App\Models\Company\Incident;
use App\Models\Company\Note;
use App\Models\Company\Task;
use App\Models\Driver\Driver;
use App\Models\Driver\EmploymentPeriod;
use App\Models\Driver\EmploymentVerification;
use App\Models\Driver\Endorsement;
use App\Models\Driver\LicenseDetail;
use App\Models\Driver\MedDetail;
use App\Models\Driver\Termination;
use App\Models\Driver\Truck;
use App\Models\Registration\RegistrationLink;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class DriverService
{
    public function getDriverDetails($driverId, $data)
    {

        $driver = Driver::findOrFail($driverId);
        $response = null;
        if ($data['category']=='tasks') {

            $response= Task::with(['assigneed'])->where('driver_id', $driver->id)
                ->where('category', $data['under_category'])
                ->orderBy('due_date', 'desc')
                ->paginate();

        }elseif ($data['category']=='notes') {
            $notes = Note::where('driver_id', $driver->id)
                ->whereDate('show_at', Carbon::today())
                ->orderBy('show_at', 'desc')
                ->paginate();
            $notes->getCollection()->transform(function ($note) {
                $note->assigned_users = $note->users(); // adds 'assigned_users' property
                return $note;
            });

            $response = $notes;
        }
        elseif ($data['category']=='documents') {
            $response  = Document::with('files')->where('driver_id', $driver->id)->where('category_id', $data['under_category'])
                ->paginate();
        }
        elseif ($data['category']=='employment') {
            if($data['under_category']=='outgoing'){
                $response = EmploymentVerification::with('events','responses','company')->where('driver_id', $driver->id)->where('created_by_company',$driver->company_id)->latest()->paginate();
            }elseif($data['under_category']=='incoming'){
                $response = EmploymentVerification::with('events','responses','company')->where('driver_id', $driver->id)->where('company_id',$driver->company_id)->latest()->paginate();
            }else{
                $response = EmploymentPeriod::with('createdBy')->where('driver_id', $driver->id)->where('company_id',$driver->company_id)->latest()->paginate();
            }
        }elseif ($data['category']=='incidents') {

            $response['response']  = Incident::with('claims')->where('type',$data['under_category'])->where('driver_id', $driver->id)->paginate();
        }
        return $response;
    }
    public function getDriverIncidentAnalytics($driverId)
    {
        $types = [
            'accident',
            'citations',
            'inspections',
            'clean',
            'biolations',
            'claims',
            'other_damage',
            'other_incidents',
        ];

        $counts = Incident::select('type', DB::raw('count(*) as total'))
            ->groupBy('type')
            ->pluck('total', 'type')
            ->toArray();
        $result = [];
        foreach ($types as $type) {
            $result[] = [
                'type'  => $type,
                'count' =>$type=='claims'? Claim::where('driver_id', $driverId)->count() : $counts[$type] ?? 0,
            ];
        }
        return $result;
    }
    public function addTask($request){
        $data= $request->validated();
        $data['status']='in_progress';
        $data['assigned_by']=auth()->id();
        $data['category']='manual';

        $task = Task::create($data);
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $path = $file->store('tasks', 'public');
                $task->attachments()->create([
                    'file_path' => $path,
                    'original_name' => $file->getClientOriginalName(),
                ]);
            }
        }
        return $task;
    }
    public function addDriver($data, $request)
    {
        return match ($data['type']) {
            'information' => $this->postPersonalInformation($request),
            'license' => $this->postLicense($request),
            'address' => $this->postAddress($request),
            'files' => $this->postFiles($request),
            'confirm' => $this->postConfirm($request),
            default => throw new \InvalidArgumentException('Invalid application type'),
        };
    }

    protected function postPersonalInformation(Request $request)
    {
        $data = $request->validate([
            'first_name' => 'required|string',
            'middle_name' => 'nullable|string',
            'email' => 'nullable|email',
            'last_name' => 'required|string',
            'ssn_sin' => 'nullable|string|unique:personal_information,ssn_sin',
            'date_of_birth' => 'required|date_format:Y-m-d',
            'application_date' => 'required|date_format:Y-m-d',
            'med_expire_date' => 'required|date_format:Y-m-d',
            'company_code' => 'required|string',
            'position_dot' => 'required|numeric|between:0,1',
        ]);

        return DB::transaction(function () use ($data) {

            $company = Company::where('user_id', Auth::id())->firstorfail();

            $driver = Driver::create([
                'ssn_sin' => $data['ssn_sin'] ?? null,
                'first_name' => $data['first_name'],
                'middle_name' => $data['middle_name'] ?? null,
                'last_name' => $data['last_name'],
                'date_of_birth' => $data['date_of_birth'],
                'position_dot' => $data['position_dot'],
                'company_id' => $company->id
            ]);
            $driver->personal_information()->create([
                'first_name' => $data['first_name'],
                'middle_name' => $data['middle_name'] ?? null,
                'last_name' => $data['last_name'],
                'date_of_birth' => $data['date_of_birth'],
            ]);
            //for med
            $documentType = DocumentType::find(4); //4- for med

            $driver->employerInformationSingle()->create([
                'code' => $data['company_code'],
            ]);
            $payload = [
                'user_id' => auth()->id(),
                'driver_id' => $driver->id,
                'category_id' =>$documentType->category_id,
                'document_type_id' => $documentType->id,
                'name' => $documentType->name,
                'number'=> null,
                'expires_at' => $data['med_expire_date'] ?? null,
                'current'=>true,
                'uploaded_by' => 'admin',
                'status' => isset($validate['med_expire_date']) &&
                now()->gt($validate['med_expire_date']) ? 'expired' : 'valid',
            ];

            Document::create($payload);

            EmploymentPeriod::create([
                'driver_id' => $driver->id,
                'company_id' => $company->id,
                'start_date' => $data['application_date'],
                'end_date' => null,
                'status'=>'active',
                'notes' => 'New Employed',
                'created_by' => auth()->id(),
            ]);
            return $driver->employee_id;
        });
    }

    protected function postLicense(Request $request)
    {
        $data = $request->validate([
            'employee_id' => 'required|exists:drivers,employee_id',
            'type_id' => 'required|numeric|exists:document_types,id',
            'cdl_class_id' => 'required_if:type_id,1|numeric|exists:cdlclasses,id',
            'number' => 'required',
//            'state_id' => 'required|exists:states,id',
            'expires_at' => 'required|date_format:Y-m-d',
            'restrictions' => 'nullable|array',
            'restrictions.*' => 'string',
            'endorsements' => 'required|array',
            'endorsements.*' => 'string',
            'twic_card' => 'nullable|file|mimes:pdf,png,jpg,jpeg|max:10048',
        ]);

        $data['state_id'] = 1;


        return DB::transaction(function () use ($data, $request) {
            $driver = Driver::where('employee_id', $data['employee_id'])->first();
            $documentType = DocumentType::find($data['type_id']);
            $payload = [
                'user_id' => auth()->id(),
                'driver_id' => $driver->id,
                'category_id' =>$documentType->category_id,
                'document_type_id' => $documentType->id,
                'name' => $documentType->name,
                'number'=> $data['number']?? null,
                'expires_at' => $data['expires_at'] ?? null,
                'current'=>true,
                'cdl_class_id'=>$data['cdl_class_id'] ?? null,
                'uploaded_by' => auth()->user()?->role === 'admin' ? 'admin' : 'driver',
                'status' => isset($validate['expires_at']) &&
                now()->gt($validate['expires_at']) ? 'expired' : 'valid',
            ];

            $endorsement = Endorsement::create(['driver_id' => $driver->id, 'endorsements' => $data['endorsements']]);
            if ($request->hasFile('twic_card')) {
                if ($endorsement->twic_card_path && Storage::disk('public')->exists($endorsement->twic_card_path)) {
                    Storage::disk('public')->delete($endorsement->twic_card_path);
                }
                $path = $request->file('twic_card')->store('twic_cards', 'public');
                $endorsement->twic_card_path = $path;
                $endorsement->save();
            }
            Document::create($payload);
            return $driver->employee_id;
        });

    }

    protected function postAddress(Request $request)
    {
        $data = $request->validate([
            'employee_id' => 'required|exists:drivers,employee_id',
//            'country_id' => 'required|string|exists:countries,id',
            'address' => 'required|string',
            'house' => 'required|string',
//            'city_id' => 'required|string|exists:cities,id',
//            'state_id' => 'required|string|exists:states,id',
            'zip' => 'required|string',
            'email' => 'required|email',
            'phone' => 'required|string',
        ]);
        $data['country_id']= 1;
        $data['city_id'] =1;
        $data['state_id'] = 1;
        return DB::transaction(function () use ($data) {
            $driver = Driver::where('employee_id', $data['employee_id'])->first();
            $driver->address()->create([
                'country_id' => $data['country_id'],
                'address' => $data['address'],
                'house' => $data['house'],
                'city_id' => $data['city_id'],
                'state_id' => $data['state_id'],
                'zip' => $data['zip'],
                'currently_live' => true
            ]);
            $driver->personal_information()->update([
                'email' => $data['email'],
                'email_confirmed' => true,
            ]);

            $driver->primary_phone = $data['phone'];
            $driver->save();
            return $driver->employee_id;
        });
    }
    protected function postFiles(Request $request)
    {
        $data = $request->validate([
            'employee_id' => 'required|exists:drivers,employee_id',
            'type_id' => 'required|numeric|exists:document_types,id',
            'side' => 'required|string|in:front,back,default',
            'file'=>'required|array',
            'file.*' => 'file|mimes:jpg,jpeg,png,pdf|max:10240',
        ]);

        return DB::transaction(function () use ($data,$request) {
            $driver = Driver::where('employee_id', $data['employee_id'])->first();
            $document = Document::where('driver_id', $driver->id)->where('document_type_id', $data['type_id'])->firstorfail();
            if( $data['type_id']>=1 && $data['type_id']<=3){
                $this->storeFiles($document, $data['file'], $data['side']);
            }else{
                $this->storeFiles($document, $data['file']);
            }
            return $driver->employee_id;

        });
    }
    public function postConfirm(Request $request)
    {
        $data = $request->validate([
            'employee_id' => 'required|exists:drivers,employee_id',
            'link'=>'required|numeric|between:0,1',
        ]);
        $driver = Driver::where('employee_id', $data['employee_id'])->first();
        $driver->driver_temp_token = (string) Str::orderedUuid();
        $driver->save();
        $data['driver_token'] = (string)$driver->driver_temp_token;
        $data['company_id']=$driver->company_id;
        $data['driver_id']=$driver->id;
        $data['purpose'] = 'login';
        $data['user_id'] = Auth::id();
        $regLink = RegistrationLink::create($data);
        $d['name']= $driver->first_name.' '.$driver->middle_name.' '.$driver->last_name;
        $d['employee_id'] = $driver->employee_id;
        $d['link']= $regLink->link;
        $d['name']= $driver->first_name.' '.$driver->middle_name.' '.$driver->last_name;
        $d['employee_id'] = $driver->employee_id;
        return $d;
    }
    public function addFiles($data,$document)
    {
        $this->storeFiles($document, $data['files']);
        return 'File successefully uploaded';
    }
    protected function storeFiles(Document $document, array $files, string $side=null): void
    {
        foreach ($files as $file) {
            $path = $file->storeAs(
                'driver-documents',
                Str::orderedUuid().rand(1,500).'.'.$file->getClientOriginalExtension(),
                'public'
            );

            $document->files()->create([
                'file_name' => $file->getClientOriginalName(),
                'file_path' => $path,
                'file_size' => $file->getSize(),
                'mime_type' => $file->getMimeType(),
                'side' => $side,
            ]);
        }
    }
    public function drivers_change_status($data)
    {
        Driver::where('id', $data['driver_id'])->update(['status' => $data['status']]);
        Termination::where('driver_id', $data['driver_id'])->delete();
        return 'Status changed to ' . $data['status'];
    }

    public function drivers_change_profile($data)
    {
        Driver::where('id', $data['driver_id'])
            ->update(Arr::except($data, ['driver_id', 'truck_name', 'truck_number']));
        $truck = Truck::updateOrCreate(
            ['driver_id' => $data['driver_id']], // search criteria
            [
                'name' => $data['truck_name'],  // columns in trucks table
                'number' => $data['truck_number'],
            ]
        );
        return 'Profile updated';
    }

}
