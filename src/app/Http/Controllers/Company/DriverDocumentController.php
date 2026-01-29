<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Http\Requests\Company\AddImageToDocumentRequest;
use App\Http\Requests\Company\DriverDocumentRequest;
use App\Models\Company\Company;
use App\Models\Company\Document;
use App\Models\Company\DocumentFile;
use App\Models\Driver\Driver;
use App\Models\Driver\PersonalInformation;
use App\Services\Company\DriverDocumentService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class DriverDocumentController extends Controller
{
    public function __construct(
        protected DriverDocumentService $service
    ) {}

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
    public function addFiles(AddImageToDocumentRequest $request, $company_id){
        $data= $request->validated();
        Driver::where('id',$data['driver_id'])->where('company_id',$company_id)->firstorfail();
        $document = Document::find($data['document_id']);
        return $this->safe(fn () =>response()->success($this->service->addFiles($data,$document),Response::HTTP_CREATED));
    }
    public function deleteFiles($company_id,$id)
    {

        $file = DocumentFile::with('document.driver')
            ->findOrFail($id);
        abort_if(
            $file->document->driver->company_id !== $company_id,
            403,
            'You cannot delete files from another company'
        );
        return $this->safe(fn () =>response()->success($this->service->deleteFile( $file),Response::HTTP_NO_CONTENT));
    }
    public function index(Request $request)
    {
        return $this->safe(fn () =>
        response()->success($this->service->listByDriver($request->get('user_id'))));
    }

    public function store(DriverDocumentRequest $request, $company_id)
    {
        $data = $request->validated();

        $document = null;
        if(isset($data['id'])){
            $document = Document::find($data['id']);
        }

        Driver::where('id',$data['driver_id'])->where('company_id',$company_id)->firstorfail();
        return match ($data['document_type_id']) {
            '1' => $this->postmaindocs($data,$request,$document,$company_id),
            '2' => $this->postmaindocs($data,$request,$document,$company_id),
            '3' => $this->postmaindocs($data,$request,$document,$company_id),
            '4' => $this->postmaindocs($data,$request,$document,$company_id),
            '5' => $this->postotherdocs($data,$request,$document,$company_id),
            '6' => $this->postotherdocs($data,$request,$document,$company_id),
            '7' => $this->postotherdocs($data,$request,$document,$company_id),
            default => throw new \InvalidArgumentException('Invalid application type'),
        };
    }
    protected function postotherdocs($data,$request, $document,$company_id){
        $validate = $request->validate([
            //            'state_id' => 'required|exists:states,id',
            'category_id' => [
                'sometimes',
                'exists:document_categories,id',
                function ($attribute, $value, $fail) use ($data) {
                    if (in_array($data['document_type_id'], ['7']) && !$value) {
                        $fail("The $attribute field is required for document types 7");
                    }
                }
            ],
            'name' => [
                $data['document_type_id'] == '7' ? 'required' : 'nullable',
                'string',
                'max:150',
            ],
            'number' => [
                $data['document_type_id'] === '7' ? 'nullable' : 'required',
                'string',
                'max:150',
            ],
            'expires_at' => 'required|date_format:Y-m-d|after_or_equal:issue_at',
            'files' =>'required|array',
            'files.*' => ['file','mimes:pdf,png,jpg,jpeg','max:10048'],

        ]);
        return $this->safe(fn () =>response()->success($this->service->postotherdocs($data,$validate,$document,$company_id),Response::HTTP_CREATED));

    }
    protected function postmaindocs($data, Request $request, $document, $company_id)
    {

        $validate = $request->validate([

            'class_id' => [
                'sometimes',
                'exists:cdlclasses,id',
                function ($attribute, $value, $fail) use ($data) {
                    if (in_array($data['document_type_id'], ['1','2']) && !$value) {
                        $fail("The $attribute field is required for document types 1, 2");
                    }
                }
            ],
            'number' => 'required|string|max:150',
            'issue_at' => 'nullable|date_format:Y-m-d',
            'state_id'=>'required|exists:states,id',
            'expires_at' => 'required|date_format:Y-m-d|after_or_equal:issue_at',
            'endorsements' => [
                'sometimes',
                'array',
                function ($attribute, $value, $fail) use ($data) {
                    if (in_array($data['document_type_id'], ['1','2']) && empty($value)) {
                        $fail("The $attribute field is required for document types 1, 2");
                    }
                }
            ],
            'endorsements.*' => 'string',
            'restrictions' =>[
                'sometimes',
                'array',
                function ($attribute, $value, $fail) use ($data) {
                    if (in_array($data['document_type_id'], ['1','2']) && empty($value)) {
                        $fail("The $attribute field is required for document types 1, 2");
                    }
                }
            ],
            'restrictions.*' => 'numeric|exists:restriction_types,id',
            'current'=>'required|numeric|between:0,1',
            'side' => 'nullable',
            'files' => [
                in_array($data['document_type_id'], ['4','5']) ? 'required' : 'nullable',
                'array'
            ],
            'files.*' => ['file','mimes:pdf,png,jpg,jpeg','max:10048'],
            'files_front' => [
                in_array($data['document_type_id'], ['1','2','3']) ? 'required' : 'nullable',
                'array'
            ],
            'files_front.*' => ['file','mimes:pdf,png,jpg,jpeg','max:10048'],

            'files_back' => [
                in_array($data['document_type_id'], ['1','2','3']) ? 'required' : 'nullable',
                'array'
            ],
            'files_back.*' => ['file','mimes:pdf,png,jpg,jpeg','max:10048'],
        ]);

        return $this->safe(fn () =>response()->success($this->service->postmaindocs($data,$validate, $document, $company_id),Response::HTTP_CREATED));
    }
    public function destroy(Document $driverDocument)
    {
        return $this->safe(fn () =>
        response()->success(
            $this->service->delete($driverDocument)
        )
        );
    }
}
