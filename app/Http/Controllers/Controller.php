<?php

namespace App\Http\Controllers;

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

    public function generate()
    {
        $dashboardId = "416d12b6-dd72-4df7-9c07-59acd600cfff";
        $accountId = config('services.quicksight.account');

        $experienceConfiguration = [
            "Dashboard" => [
                "InitialDashboardId"=> "416d12b6-dd72-4df7-9c07-59acd600cfff"
            ]
        ];

        try {
            /*$result = $quick->generateEmbedUrlForAnonymousUser([
                'AwsAccountId' => $accountId,
                'Namespace' => 'default',
                'AuthorizedResourceArns' => [
                    "arn:aws:quicksight:ap-northeast-1:251286709479:dashboard/416d12b6-dd72-4df7-9c07-59acd600cfff"
                ],
                'ExperienceConfiguration' => $experienceConfiguration,
                'SessionLifetimeInMinutes' => 600,
            ]);*/

            $result = $this->client->GetDashboardEmbedUrl([
                'AwsAccountId' => $accountId,
                'DashboardId' => "416d12b6-dd72-4df7-9c07-59acd600cfff",
                'IdentityType' => 'IAM',
                'ResetDisabled' => true,
                'SessionLifetimeInMinutes' => 600,
                'UndoRedoDisabled' => false
            ]);
        } catch (AwsException $e) {
            dd($e);
        }

        return response()->json([
            'code' => 200,
            'message' => 'Operation successfull',
            'dashboard' => $result,
        ]);

        dd($quick);
    }

    public function register()
    {
        try {
            $accountId = config('services.quicksight.account');
            $result = $this->client->registerUser([
                'AwsAccountId' => $accountId, // REQUIRED
                'Email' => 'nhtienptit@gmail.com', // REQUIRED
                'IdentityType' => 'IAM', // REQUIRED
                'Namespace' => 'default', // REQUIRED
                'UserRole' => 'READER'
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
}
