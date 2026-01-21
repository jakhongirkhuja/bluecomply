<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Http\Requests\Company\MessageHistorySentRequest;
use App\Models\Company\Incident;
use App\Models\Company\MessageHistory;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Str;

class MessageController extends Controller
{
    public function __construct(){

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
    public function messagePost(MessageHistorySentRequest $request, $companyId,$id){
        $data = $request->validated();

        return $this->safe(function () use ($data,$companyId,$id) {
            $message = MessageHistory::with('attachments')->create([
                'company_id' => $companyId,
                'driver_id' => $id,
                'sender_id' => auth()->id(),
                'message' => $data['message'],
                'status' => 'sent',
            ]);
            if(isset($data['files'])){
                $this->storeFiles($message, $data['files'], $data['type'] ?? null);
            }


            //send sms
            return response()->success($message);
        });

    }
    public function messages( $companyId,$id){
        $message = MessageHistory::with('attachments')->where('company_id',$companyId)->where('driver_id',$id)->latest()->limit(10)->get();
        return response()->success($message);
    }
    protected function storeFiles(MessageHistory $message, array $files, $type=null): void
    {
        foreach ($files as $file) {
            $path = $file->storeAs(
                'company-message',
                Str::orderedUuid().rand(1,500).'.'.$file->getClientOriginalExtension(),
                'public'
            );

            $message->attachments()->create([
                'type'=>$type,
                'file_name' => $file->getClientOriginalName(),
                'file_path' => $path,
                'file_size' => $file->getSize(),
                'mime_type' => $file->getMimeType(),
            ]);
        }
    }
}
