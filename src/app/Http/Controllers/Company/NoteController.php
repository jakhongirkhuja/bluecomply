<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Http\Requests\Company\StoreNoteRequest;
use App\Models\Company\Note;
use App\Services\Company\NoteService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class NoteController extends Controller
{
    public function __construct(protected NoteService $service)
    {
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
    public function store(StoreNoteRequest $request, $comapny_id)
    {
        $data = $request->validated();
        $userIds = $data['user_id'];

        $eligibleUsers = $this->service->filterEligibleUsers($userIds, auth()->id());
//        if (count($eligibleUsers) === 0) {
//            return response()->error('No eligible users for this company');
//        }

        $data['user_id'] = $eligibleUsers;
        $data['created_by']= auth()->id();

        return $this->safe(fn() => response()->success($this->service->create($data, $comapny_id),Response::HTTP_CREATED));
    }
    public function update(StoreNoteRequest $request, Note $note, $comapny_id)
    {
        $data = $request->validated();
        if (isset($data['user_id'])) {
            $eligibleUsers = $this->service->filterEligibleUsers($data['user_id'], auth()->id());
            if (count($eligibleUsers) === 0) {
                return response()->error('No eligible users for this company');
            }
            $data['user_id'] = $eligibleUsers;
        }
        return $this->safe(fn() => response()->success($this->service->update($note, $data, $comapny_id),Response::HTTP_OK));
    }
    public function destroy(Note $note, $comapny_id)
    {
        $note->delete();
        return response()->success($note, Response::HTTP_NO_CONTENT);
    }
}
