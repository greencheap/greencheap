<?php
namespace GreenCheap\Installer\Controller;

use GreenCheap\Application as App;
use GreenCheap\OAuth2Kernel;
use Symfony\Component\HttpFoundation\Request;
use \Curl\Curl;

/**
 * @Access("system: software updates", admin=true)
 * @Route("/api" , name="api")
 */
class ApiUpdateController
{
    /**
     * @Route("/download-release" , methods="GET")
     * @Request({"constraint":"string" , "access_token":"string"} , csrf=true)
     */
    public function getClientDownloadAction(string $constraint = 'stable' , string $access_token = '')
    {
        $url = App::get('system.api').'/api/brain/download/download?';

        $parameters = http_build_query([
            'constraint' => $constraint
        ], '', '&'); 

        $zip_name = tempnam(App::get('path.temp'), 'update_');
        $path = App::get('path.temp').'/';

        $curl = new Curl();
        $curl->setOpt(CURLOPT_ENCODING , '');
        $curl->setOpt(CURLOPT_TIMEOUT, 600);
        $curl->setOpt(CURLOPT_CONNECTTIMEOUT, 600);
        $curl->setOpt(CURLOPT_RETURNTRANSFER, true);
        $curl->setOpt( CURLOPT_SSL_VERIFYPEER, true);
        $curl->download($url.$parameters, $zip_name);

        App::session()->set('system.update', $zip_name);
        if ($curl->error) {
            return App::abort(500 , $curl->errorMessage);
        } else {
            return ['message' => $zip_name];
        }
    }

    /**
     * Yüklenen yeni version buradan bir kontürole geçer temp dosyasına geçici veri oluşturulur içeriği alınır ve temp olarak devreye girer.
     * @Route("/upload-package" , methods="POST")
     * @Request(csrf=true)
     * @param Request $request
     * @return array
     */
    public function uploadPackageAction(Request $request)
    {
        $file = $request->files->get('_package');
        $system_version = OAuth2Kernel::versionParse($request->request->get('_version'));
        $zip_name = tempnam(App::get('path.temp'), 'update_');

        \preg_match('/(.+[\/])(update.+)/' , $zip_name , $pregMatch);

        if(!$file->move($pregMatch[1] , $pregMatch[2])){
            return App::abort(500 , __('Problem'));
        }

        $versionsData = [];
        $zip = new \ZipArchive;
        if($zip->open($pregMatch[0])){
            $versionsData['composer']   =   \json_decode($zip->getFromName('composer.json') , true);
            $versionsData['package']    =   \json_decode($zip->getFromName('package.json') , true);
            $versionsData['readme']     =   $zip->getFromName('README.md');
            $versionsData['changelog']  =   $zip->getFromName('CHANGELOG.md');
        }else{
            return App::abort(400 , __('Not Open Zip File'));
        }

        $versionsData['path']       =   $pregMatch[1];
        $versionsData['name']       =   $pregMatch[2];
        $versionsData['fullPath']   =   $pregMatch[0];
        $versionsData['version']    =   OAuth2Kernel::versionParse($versionsData['composer']['version']);

        if( $versionsData['version']->constraint != $system_version->constraint ){
            App::file()->delete($versionsData['fullPath']);
            return App::abort(409 , __('Constraint Problem'));
        }

        if(version_compare($system_version->version , $versionsData['version']->version , '>')){
            App::file()->delete($versionsData['fullPath']);
            return App::abort(409 , __('Error: System version, New package new_version' , ['version' => $system_version->version , 'new_version' => $versionsData['version']->version]));
        }

        App::session()->set('system.update', $zip_name);
        return $versionsData;
    }

    /**
     * @Route("/remove-package" , methods="GET")
     * @Request({"fullpath":"string"} , csrf=true)
     * @param string $fullPath
     * @return bool
     */
    public function removeTempPackageAction(string $fullPath = '')
    {
        if(App::file()->exists($fullPath)){
            App::file()->delete($fullPath);
        }
        return true;
    }
}
?>
