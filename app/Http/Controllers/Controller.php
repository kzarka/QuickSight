<?php

namespace App\Http\Controllers;

use App\Services\CSVService;
use App\Services\MaintainLogService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Aws\QuickSight\QuickSightClient;
use Aws\Exception\AwsException;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public $client;

    public function __construct()
    {
        $quick = new QuickSightClient([
            'version' => 'latest',
            'region'  => 'ap-northeast-1',
            'credentials' => [
                'key'    => config('services.quicksight.key'),
                'secret' => config('services.quicksight.secret'),
            ]
        ]);
        $this->client = $quick;
    }

    public function index()
    {
        $link = $this->generateConsoleOnly();
        return view('welcome')->with(['link' => $link]);
    }

    public function generate($id)
    {
        $accountId = config('services.quicksight.account');
        $reader = config('services.quicksight.reader');

        try {

            $result = $this->client->GetDashboardEmbedUrl([
                'AwsAccountId' => $accountId,
                'DashboardId' => $id,
                'IdentityType' => 'QUICKSIGHT',
                'UserArn' => 'arn:aws:quicksight:ap-northeast-1:' . $accountId . ':user/default/' . $reader,
                'ResetDisabled' => true,
                'UndoRedoDisabled ' => true,
                'SessionLifetimeInMinutes' => 600,
                'UndoRedoDisabled' => false
            ]);
        } catch (AwsException $e) {
            return response()->json([
                'code' => 404,
                'dashboard' => '',
            ]);
        }

        return response()->json([
            'code' => 200,
            'dashboard' => $result->get('EmbedUrl'),
        ]);
    }

    public function generateConsole()
    {
        $accountId = config('services.quicksight.account');
        $reader = 'n.htienptit@gmail.com';

        try {
            //dd('arn:aws:quicksight:ap-northeast-1:' . $accountId . ':user/default/' . $reader);
            $result = $this->client->GenerateEmbedUrlForRegisteredUser([
                "AwsAccountId" => $accountId,
                "ExperienceConfiguration" => [
                    "QuickSightConsole" => [
                        "InitialPath" => '/start',
                    ]
                ],
                "UserArn" => 'arn:aws:quicksight:ap-northeast-1:' . $accountId . ':user/default/' . $reader,
                "SessionLifetimeInMinutes" => 600
            ]);
        } catch (AwsException $e) {
            dd($e->getMessage());
            return response()->json([
                'code' => 404,
                'dashboard' => '',
            ]);
        }

        return response()->json([
            'code' => 200,
            'dashboard' => $result->get('EmbedUrl'),
        ]);
    }
    public function register()
    {
        try {
            $accountId = config('services.quicksight.account');
            $result = $this->client->registerUser([
                'AwsAccountId' => $accountId, // REQUIRED
                'Email' => 'nhtienptit@gmail.com', // REQUIRED
                'IdentityType' => 'QUICKSIGHT', // REQUIRED
                'Namespace' => 'default', // REQUIRED
                'UserRole' => 'READER',
                'UserName' => 'tiennh2',
            ]);
        } catch (AwsException $e) {
            dd($e);
        }
        return response()->json([
            'code' => 200,
            'message' => 'Operation successfull',
            'dashboard' => $result,
        ]);
    }

    public function generateConsoleOnly()
    {
        $accountId = config('services.quicksight.account');
        $reader = 'n.htienptit@gmail.com';

        try {
            //dd('arn:aws:quicksight:ap-northeast-1:' . $accountId . ':user/default/' . $reader);
            $result = $this->client->GenerateEmbedUrlForRegisteredUser([
                "AwsAccountId" => $accountId,
                "ExperienceConfiguration" => [
                    "QuickSightConsole" => [
                        "InitialPath" => '/start',
                    ]
                ],
                "UserArn" => 'arn:aws:quicksight:ap-northeast-1:' . $accountId . ':user/default/' . $reader,
                "SessionLifetimeInMinutes" => 600
            ]);
        } catch (AwsException $e) {
            return '';
        }

        return $result->get('EmbedUrl');
    }

    public function buildCsv(CSVService $service)
    {
        $service->write();
    }

    public function buildMaintainLog(MaintainLogService $service)
    {
        $service->write();
    }
}
