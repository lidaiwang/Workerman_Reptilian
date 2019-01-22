<?php

class SendEmail
{
    protected $log;
    private $mail;
    //发件人邮箱
    private $address = array(
        array(
            'showname' => '',
            'name' => '',
            'username' => '',
            'pwd' => ''),
    );

    public function require_()
    {
        $HOME_PATH = dirname(dirname(__FILE__));
        require_once $HOME_PATH . "/conf/config.php";
        require_once $HOME_PATH . "/lib/Email/class.phpmailer.php";
        require_once $HOME_PATH . "/lib/Email/class.smtp.php";

        $this->log = Loggers::getInstance("run");
    }

    /**
     * 构造函数
     * @author xym Created At 2013-2-1
     */
    public function __construct()
    {
        $this->require_();

        $mail = new PHPMailer();
        $mail->IsSMTP();                                    // 使用SMTP方式发送
        $mail->Host = 'emaws.com';                    //smtp服务器名
        $mail->SMTPAuth = true;                                // 启用SMTP验证功能
        $mail->SMTPSecure = 'ssl';
        $count = count($this->address);
        $num = mt_rand(0, $count - 1);
        $mail->Username = $this->address[(int)$num]['username'];    //服务器认证用户名(完整的email地址)
        $mail->Password = $this->address[(int)$num]['pwd'];    //认证密码
        $mail->Port = 465;                                        //端口号
        $mail->From = $this->address[(int)$num]['name'];        //发送人邮箱地址
        $mail->FromName = $this->address[(int)$num]['showname'];                                //发送人昵称
        $mail->WordWrap = 50;                                    //50个字符自动换行
        $mail->IsHTML(true);                                    // 是否HTML格式邮件
        $mail->CharSet = "utf-8";
        $mail->SetLanguage('zh_cn');                            //设置错误语言
        $this->mail = $mail;
    }

    public function send_($emailAddress, $subject, $content)
    {
        $mail = new PHPMailer();
        $mail->isSMTP();// 使用SMTP服务
        $mail->CharSet = "utf8";// 编码格式为utf8，不设置编码的话，中文会出现乱码
        $mail->Host = "smtp.163.com";// 发送方的SMTP服务器地址
        $mail->SMTPAuth = true;// 是否使用身份验证
        $mail->Username = EMAIL_USERNAME;// 发送方的163邮箱用户名
        $mail->Password = EMAIL_PASSWOED;// 发送方的邮箱密码，注意用163邮箱这里填写的是“客户端授权密码”而不是邮箱的登录密码！
        $mail->SMTPSecure = "ssl";// 使用ssl协议方式
        $mail->Port = 994;// 163邮箱的ssl协议方式端口号是465/994
        $mail->From = EMAIL_NAME;
        $mail->Helo = EMAIL_NAME;
        $mail->setFrom(EMAIL_USERNAME, EMAIL_NAME);// 设置发件人信息，如邮件格式说明中的发件人，这里会显示为Mailer(xxxx@163.com），Mailer是当做名字显示
        $mail->addAddress($emailAddress, EMAIL_NAME);// 设置收件人信息，如邮件格式说明中的收件人，这里会显示为Liang(yyyy@163.com)
        $mail->IsHTML(true);
        $mail->Subject = $subject;// 邮件标题
        $mail->Body = $content;// 邮件正文

        $array = array('success' => true, 'error' => '验证邮件发送成功！', 'emailAddress' => $emailAddress, 'subject' => $subject, 'content' => $content,);
        if (!$mail->Send()) {
            $array = array('success' => false, 'error' => '发送失败，原因：' . $this->mail->ErrorInfo, 'emailAddress' => $emailAddress, 'subject' => $subject, 'content' => $content,);
        }

        $this->log->warning("邮件发送记录", -1, $array);
        return $array;
    }

    /**
     * 发送邮件
     * @param string | array $emailAddress
     * @param $subject
     * @param $content
     * @return array
     * @author xym Created At 2013-2-1
     */
    public function send($emailAddress, $subject, $content)
    {
        $this->mail->ClearAllRecipients();//清空所有地址
        if (is_array($emailAddress)) {
            foreach ($emailAddress as $oneEmail) {
                $this->mail->AddAddress($oneEmail);
            }
        } else {
            $this->mail->AddAddress($emailAddress);
        }
        $this->mail->Subject = $subject;    //邮件主题
        $this->mail->Body = $content;    //邮件内容
        if (!$this->mail->Send()) {
            return array('success' => false, 'error' => '发送失败，原因：' . $this->mail->ErrorInfo);
        }
        return array('success' => true, 'error' => '验证邮件发送成功！');
    }
}