<?php

namespace arabic0513\Eimzo\Http\Controllers;

use App\Models\SignedDocs;
use arabic0513\Eimzo\Jobs\EriJoinSignJob;
use arabic0513\Eimzo\Jobs\EriSignJob;
use arabic0513\Eimzo\Requests\SignRequest;
use arabic0513\Eimzo\Services\EimzoService;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use App\Observers\SignDocsObserver;

class EimzoSignController extends Controller
{
    private EimzoService $eimzoService;
    public function __construct()
    {
        $this->eimzoService = new EimzoService();
    }

    public function index()
    {
        $signs = SignedDocs::all();
        return view('arabic0513.eimzo.sign.master', compact('signs'));
    }

    public function verifyPks(SignRequest $request)
    {

        try {
            $text = $request->data;
            $document = SignedDocs::where('text', $text)->where('role_id',auth()->user()->role_id)->first();


            if($document){
                $new = $request->pkcs7;
                $old = $document->pkcs;
                $newPkcs = $this->eimzoService->joinSigns($old,$new);
                if(!$newPkcs)
                    return redirect()->route('eimzo.back')->with('danger', 'Fix Eimzo Service! Error in newPkcs');
                $signers = $this->eimzoService->getXML($newPkcs);

                if(!$signers)
                    return redirect()->route('eimzo.back')->with('danger', 'Fix Eimzo Service! Error in getting info');

                $this->dispatchNow(new EriJoinSignJob($request, $signers, $document, $newPkcs[0]));

            }
            else {
                $pkcs7[] = $request->pkcs7;
                $signers = $this->eimzoService->getXML($pkcs7);
                if(!$signers)
                    return redirect()->route('eimzo.back')->with('danger', 'Fix Eimzo Service!');
                $this->dispatchNow(new EriSignJob($request, $signers));
                if(__DIR__ . 'App\Observers\SignDocsObserver' !== null)
                {
                    $new = new SignDocsObserver();
                    $new->updated($request->application_id);
                }
            }
            return redirect()->route('eimzo.back')->with('success', 'Signed');
        } catch (\Exception $exception) {
            return redirect()->route('eimzo.back')->with('danger', 'Something went wrong! Contact developer!');
        }

    }

    public function joinTwoPks(SignRequest $request)
    {
        try {
            return redirect()->route('sign.index')->with('success', 'Signed');
        } catch (\Exception $exception) {
            return redirect()->route('sign.index')->with('danger', 'Something went wrong! Contact developer!');
        }
    }

    public function sign()
    {

    }

    public function docsList(Request $request)
    {
        if (isset($request->orderBy)) {
            if ($request->orderBy == 'all') {
                $data = SignedDocs::get();
            }
        }
        return $data;
    }

}
