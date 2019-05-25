<?php


namespace app\admin\controller;


use think\facade\App;

class Payment extends BaseAdmin
{
    public function index()
    {
        if ($this->request->isPost()) {
            $content = input('json');
            file_put_contents(App::getRootPath() . 'config/payment.php', $content);
            $this->success('保存成功');
        }
        $content = file_get_contents(App::getRootPath() . 'config/payment.php');
        $this->assign('json', $content);
        return view();
    }

    protected function jsonFormat($data, $indent = null)
    {

        // 对数组中每个元素递归进行urlencode操作，保护中文字符
        array_walk_recursive($data, 'jsonFormatProtect');

        // json encode
        $data = json_encode($data);

        // 将urlencode的内容进行urldecode
        $data = urldecode($data);

        // 缩进处理
        $ret = '';
        $pos = 0;
        $length = strlen($data);
        $indent = isset($indent) ? $indent : '    ';
        $newline = "\n";
        $prevchar = '';
        $outofquotes = true;

        for ($i = 0; $i <= $length; $i++) {

            $char = substr($data, $i, 1);

            if ($char == '"' && $prevchar != '\\') {
                $outofquotes = !$outofquotes;
            } elseif (($char == '}' || $char == ']') && $outofquotes) {
                $ret .= $newline;
                $pos--;
                for ($j = 0; $j < $pos; $j++) {
                    $ret .= $indent;
                }
            }

            $ret .= $char;

            if (($char == ',' || $char == '{' || $char == '[') && $outofquotes) {
                $ret .= $newline;
                if ($char == '{' || $char == '[') {
                    $pos++;
                }

                for ($j = 0; $j < $pos; $j++) {
                    $ret .= $indent;
                }
            }
            $prevchar = $char;
        }
        return $ret;
    }
}