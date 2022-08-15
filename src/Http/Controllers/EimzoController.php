<?php

namespace arabic0513\Eimzo\Http\Controllers;

use Illuminate\Http\Request;
use arabic0513\Eimzo\Requests\EriRequest;
use arabic0513\Eimzo\Services\AuthLogService;
use arabic0513\Eimzo\Services\EriService;
use Illuminate\Support\Facades\Log;

class EimzoController extends Controller
{
    public function login()
    {
        return view('auth.auth');
    }
    public function auth(Request $request)
    {
        try {
            $oneAuthService = new EriService();
            $params = $oneAuthService->makeParams($request->toArray());
            $oneAuthService->authorizeUser($params);
            AuthLogService::logAuth();
        } catch (\Throwable $th) {
            $errorMessage = "Киришда хатолик юз берди, илтимос кейинроқ уруниб кўринг.";
            if(in_array($th->getCode(), [401])) {
                $errorMessage = $th->getMessage();
            }

            Log::error(sprintf("ERI error: Message: %s, Line: %s, File: %s", $th->getMessage(), $th->getLine(), $th->getFile()));
            return redirect()->back()->with('danger', 'Something went wrong!');
        }

        return redirect()->route('site.applications.index');
    }
}
