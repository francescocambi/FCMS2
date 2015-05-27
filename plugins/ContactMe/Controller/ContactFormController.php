<?php
/**
 * User: Francesco
 * Date: 06/02/15
 * Time: 17:44
 */

namespace Plugin\ContactMe\Controller;


class ContactFormController {

    /**
     * @param \Silex\Application $app
     */
    public function getHTML($app) {

        //Analyze request
        if ( is_null($app['request']->request->get('email')) ) {

            //If POST is empty render form
            return $app['twig']->render('plugins\\ContactMe\\contactFormBlock.twig', array(
                'recaptcha_sitekey' => $app['config']->get('ContactMe.recaptcha_sitekey')
            ));
        } else {

            $data = array(
                'ref_name' => $app['request']->request->get('ref_name'),
                'ref_surname' => $app['request']->request->get('ref_surname'),
                'email' => $app['request']->request->get('email'),
                'message' => $app['request']->request->get('message')
            );

            $recaptcha_secretkey = $app['config']->get('ContactMe.recaptcha_secretkey');

            $recaptcha_response = $app['request']->get('g-recaptcha-response');

            $url = 'https://www.google.com/recaptcha/api/siteverify';
            $recaptcha_request_data = array('secret' => $recaptcha_secretkey,
                'response' => $recaptcha_response,
                'remoteip' => $_SERVER['REMOTE_ADDR']);

            // use key 'http' even if you send the request to https://...
            $options = array(
                'http' => array(
                    'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
                    'method'  => 'POST',
                    'content' => http_build_query($recaptcha_request_data),
                ),
            );
            $context  = stream_context_create($options);
            $result = file_get_contents($url, false, $context);

            $api_result = json_decode($result);

            if ($api_result->success === true) {
                //Captcha is ok, send mail
            } else {
                //Wrong captcha, display error
                return $app['twig']->render('plugins\\ContactMe\\contactFormBlock.twig', array(
                    'data' => $data,
                    'failure_message' => "Verifica non valida. Riprovare.",
                    'recaptcha_sitekey' => $app['config']->get('ContactMe.recaptcha_sitekey')
                ));
            }

            //else persist data, handle results and display op completed message
            if ($this->sendMail($app, $data) == 1)
                return $app['twig']->render('plugins\\ContactMe\\formFeedbackBlock.twig');
            else {
                $app['monolog']->addError("ContactMe -- Can't send email. -- ".json_encode($data));
                return $app['twig']->render('plugins\\ContactMe\\errorFeedbackBlock.twig');
            }
        }

    }

    public function sendMail($app, $data) {

        $conf = $app['config']->get('ContactMe');

        $headers = array();
        $headers[] = "MIME-Version: 1.0";
        $headers[] = "Content-type: text/plain; charset=iso-8859-1";
        $headers[] = "From: ".$conf['site_mail'];
        $headers[] = "Reply-To: ".$data['ref_name']." ".$data['ref_surname']." <".$data['email'].">";
        $headers[] = "X-Mailer: PHP/".phpversion();

        $to = $conf['send_to'];
        $subject = $conf['subject'];
        $email = $data['message'];

        return mail($to, $subject, $email, implode("\r\n", $headers));

    }

} 