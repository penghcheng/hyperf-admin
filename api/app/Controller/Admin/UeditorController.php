<?php


namespace App\Controller\Admin;


use App\Controller\AbstractController;


/**
 * description="富文本编辑器"
 */
class UeditorController extends AbstractController
{

    /**
     * description="富文本编辑器配置"
     */
    public function ue()
    {
        header("Access-Control-Allow-Headers: X-Requested-With,X_Requested_With");

        $CONFIG = json_decode(preg_replace("/\/\*[\s\S]+?\*\//", "", file_get_contents(PUBLIC_PATH . "/ueditor/config.json")), true);

        $action = $this->request->input('action', '');
        switch ($action) {
            case 'config':
                $result = json_encode($CONFIG);
                break;
            case 'uploadimage':
                $result = include(PUBLIC_PATH . "/ueditor/action_upload.php");
                break;
            case 'uploadfile':
                $result = include(PUBLIC_PATH . "/ueditor/action_upload.php");
                break;
            case 'uploadscrawl':
            default:
                $result = json_encode(array(
                    'state'=> '请求地址出错'
                ));
                break;
        }
        $callback = $this->request->input('callback', '');
        /* 输出结果 */
        if (!empty($callback)) {
            if (preg_match("/^[\w_]+$/", $callback)) {
                return $this->response->success( htmlspecialchars($callback) . '(' . $result . ')' );
            } else {
                return $this->response->success( json_encode(array(
                        'state' => 'callback参数不合法'
                    ))
                );
            }
        } else {
            return $this->response->success( is_array($result) ? json_encode($result) : $result );
        }
    }

}