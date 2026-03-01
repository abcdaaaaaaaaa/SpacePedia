<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require __DIR__.'/PHPMailer/src/PHPMailer.php';
require __DIR__.'/PHPMailer/src/SMTP.php';
require __DIR__.'/PHPMailer/src/Exception.php';
require_once __DIR__.'/smtp_config.php';

function mailBaseSetup($mail,$lang){
    $mail->isSMTP();
    $mail->Host = SMTP_HOST;
    $mail->SMTPAuth = true;
    $mail->Username = SMTP_USER;
    $mail->Password = SMTP_PASS;
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
    $mail->Port = SMTP_PORT;
    $mail->CharSet = 'UTF-8';
    $mail->Encoding = 'base64';
    $fromName = $lang === 'tr' ? 'uzay.info' : 'SpacePedia';
    $mail->setFrom(SMTP_USER,$fromName);
    $mail->isHTML(true);
}

function sendActivationMail($email,$username,$token,$lang){
    $mail = new PHPMailer(true);
    mailBaseSetup($mail,$lang);

    $link = SITE_URL . "/verify?token=" . $token;

    if($lang === 'tr'){
        $subject = "uzay.info hesabını onaylayın, {$username}";
        $body = "
            <p>Merhaba @{$username},</p>
            <p>uzay.info hesabınızı etkinleştirmek için aşağıdaki bağlantıya tıklayınız:</p>
            <p><a href='{$link}'>{$link}</a></p>
            <p>(Bağlantı çalışmıyorsa tarayıcının adres çubuğuna kopyalayıp yapıştır!)</p>
            <p style='font-size:10px;color:#666;'>
            Bu otomatik bir e-postadır. Eğer uzay.info’ya kayıt olmadıysanız bu e-postayı güvenle yok sayabilirsin.<br>
            Onaylanmamış hesap ve e-posta adresine ait tüm veriler 48-72 saat içinde sistemden kalıcı olarak silinecektir.
            </p>
        ";
    } else {
        $subject = "Confirm your spacepedia.info account, {$username}";
        $body = "
            <p>Hello @{$username},</p>
            <p>Click the link below to enable your SpacePedia account:</p>
            <p><a href='{$link}'>{$link}</a></p>
            <p>(If clicking does not work, try pasting it into your browser!)</p>
            <p style='font-size:10px;color:#666;'>
            This is an automated message. If you did not register with SpacePedia, you can safely ignore this email.<br>
            The unconfirmed account and all traces of your email address will be permanently deleted from our system after 48-72 hours.
            </p>
        ";
    }

    $mail->addAddress($email);
    $mail->Subject = $subject;
    $mail->Body = $body;
    $mail->send();
}

function sendResetMail($email,$username,$token,$lang){
    $mail = new PHPMailer(true);
    mailBaseSetup($mail,$lang);

    $link = SITE_URL . "/reset?token=" . $token;

    if($lang === 'tr'){
        $subject = "uzay.info parola yenileme";
        $body = "
            <p>Merhaba @{$username},</p>
            <p>uzay.info hesabınız için bir parola yenileme talebi aldık.</p>
            <p>Parolanızı yenilemek için aşağıdaki bağlantıyı kullan:</p>
            <p><a href='{$link}'>{$link}</a></p>
            <p style='font-size:10px;color:#666;'>
            Bu otomatik bir e-postadır. Eğer uzay.info için bir parola yenileme talebinde bulunmadıysanız bu e-postayı güvenle yok sayabilirsin.
            </p>
        ";
    } else {
        $subject = "spacepedia.info password reset";
        $body = "
            <p>Hello @{$username},</p>
            <p>We received a request to reset the password for your SpacePedia account.</p>
            <p>You can reset your password using the link below:</p>
            <p><a href='{$link}'>{$link}</a></p>
            <p style='font-size:10px;color:#666;'>
            This is an automated message. If you did not request a password reset for SpacePedia, you can safely ignore this email.
            </p>
        ";
    }

    $mail->addAddress($email);
    $mail->Subject = $subject;
    $mail->Body = $body;
    $mail->send();
}

function sendActivationSuccessMail($email,$username,$lang){
    $mail = new PHPMailer(true);
    mailBaseSetup($mail,$lang);
    
    $profileLink=($lang==='tr'?'https://uzay.info/@/':'https://spacepedia.info/@/').$username;

    if($lang === 'tr'){
        $subject = "uzay.info'ya hoş geldiniz, {$username}";
        $body = "
            <p>https://www.uzay.info adresinde hesabınız başarıyla oluşturulmuştur.</p>
            <p>Profil sayfanız: <a href='{$profileLink}'>{$profileLink}</a></p>
            <p>Uzay ansiklopedimizde keyifli yolculuklar ve derin keşifler dileriz!</p>
            <p style='font-size:10px;color:#666;'>
            Bu otomatik bir e-postadır. Eğer bu hesap size ait değilse, lütfen bu e-posta adresine durumu bildiriniz: ".SMTP_USER."
            </p>
        ";
    } else {
        $subject = "Welcome to spacepedia.info, {$username}";
        $body = "
            <p>You have successfully created your account on https://www.spacepedia.info.</p>
            <p>Here is your profile page: <a href='{$profileLink}'>{$profileLink}</a></p>
            <p>We wish you enjoyable journeys and deep explorations in our SpacePedia!</p>
            <p style='font-size:10px;color:#666;'>
            This is an automated message. If this account does not belong to you, please notify us by sending an email to: ".SMTP_USER."
            </p>
        ";
    }

    $mail->addAddress($email);
    $mail->Subject = $subject;
    $mail->Body = $body;
    $mail->send();
}

function sendPasswordChangedSuccessMail($email,$username,$lang){
    $mail = new PHPMailer(true);
    mailBaseSetup($mail,$lang);

    if($lang === 'tr'){
        $subject = 'Parolanız güncellendi';
        $body = "
            <p>Merhaba @{$username},</p>
            <p>Hesabınıza ait parola başarıyla güncellenmiştir.</p>
            <p style='font-size:10px;color:#666;'>
            Bu otomatik bir e-postadır. Eğer bu işlem tarafınızdan gerçekleştirilmediyse, lütfen bu e-posta adresine durumu bildiriniz: ".SMTP_USER."
            </p>
        ";
    } else {
        $subject = 'Your password has been updated';
        $body = "
            <p>Hello @{$username},</p>
            <p>The password associated with your account has been successfully updated.</p>
            <p style='font-size:10px;color:#666;'>
            This is an automated message. If this action was not performed by you, please notify us by sending an email to: ".SMTP_USER."
            </p>
        ";
    }

    $mail->addAddress($email);
    $mail->Subject = $subject;
    $mail->Body = $body;
    $mail->send();
}

function sendEmailChangeMail($email,$username,$token,$lang){
    $mail = new PHPMailer(true);
    mailBaseSetup($mail,$lang);

    $link = SITE_URL . "/email?token=" . $token;

    if($lang === 'tr'){
        $subject = "uzay.info e-posta değiştirme onayı";
        $body = "
            <p>Merhaba @{$username},</p>
            <p>uzay.info hesabınız için bir e-posta değiştirme talebi aldık.</p>
            <p>Yeni e-posta adresinizi onaylamak için aşağıdaki bağlantıya tıklayınız:</p>
            <p><a href='{$link}'>{$link}</a></p>
            <p style='font-size:10px;color:#666;'>
            Bu otomatik bir e-postadır. Eğer bu işlemi siz başlatmadıysanız bu e-postayı güvenle yok sayabilirsiniz.
            </p>
        ";
    } else {
        $subject = "spacepedia.info email change confirmation";
        $body = "
            <p>Hello @{$username},</p>
            <p>We received a request to change the email address associated with your SpacePedia account.</p>
            <p>To confirm your new email address, please click the link below:</p>
            <p><a href='{$link}'>{$link}</a></p>
            <p style='font-size:10px;color:#666;'>
            This is an automated message. If you did not request this change, you can safely ignore this email.
            </p>
        ";
    }

    $mail->addAddress($email);
    $mail->Subject = $subject;
    $mail->Body = $body;
    $mail->send();
}

function sendEmailChangedSuccessMail($email,$username,$lang){
    $mail = new PHPMailer(true);
    mailBaseSetup($mail,$lang);

    if($lang === 'tr'){
        $subject = "uzay.info e-posta adresiniz değiştirildi";
        $body = "
            <p>Merhaba @{$username},</p>
            <p>Hesabınıza ait e-posta adresi başarıyla değiştirildi.</p>
            <p style='font-size:10px;color:#666;'>
            Bu otomatik bir e-postadır. Eğer bu işlem tarafınızdan yapılmadıysa lütfen bizimle iletişime geçiniz: ".SMTP_USER."
            </p>
        ";
    } else {
        $subject = "spacepedia.info your email address has been changed";
        $body = "
            <p>Hello @{$username},</p>
            <p>The email address associated with your account has been successfully changed.</p>
            <p style='font-size:10px;color:#666;'>
            This is an automated message. If this action was not performed by you, please contact us at: ".SMTP_USER."
            </p>
        ";
    }

    $mail->addAddress($email);
    $mail->Subject = $subject;
    $mail->Body = $body;
    $mail->send();
}

function sendAccountReopenMail($email,$username,$token,$lang){
    $mail = new PHPMailer(true);
    mailBaseSetup($mail,$lang);

    $link = SITE_URL . "/reopen_account_verify?token=" . $token;

    if($lang === 'tr'){
        $subject = "uzay.info hesap yeniden açma";
        $body = "
            <p>Merhaba @{$username},</p>
            <p>Kapatılmış olan hesabınızı yeniden açmak için bir talep aldık.</p>
            <p>Hesabınızı tekrar açmak için aşağıdaki bağlantıya tıklayınız:</p>
            <p><a href='{$link}'>{$link}</a></p>
            <p style='font-size:10px;color:#666;'>
            Bu otomatik bir e-postadır. Eğer bu işlemi siz başlatmadıysanız bu e-postayı güvenle yok sayabilirsiniz.
            </p>
        ";
    } else {
        $subject = "spacepedia.info account reactivation";
        $body = "
            <p>Hello @{$username},</p>
            <p>We received a request to reopen your closed account.</p>
            <p>To reopen your account, please click the link below:</p>
            <p><a href='{$link}'>{$link}</a></p>
            <p style='font-size:10px;color:#666;'>
            This is an automated message. If you did not request this action, you can safely ignore this email.
            </p>
        ";
    }

    $mail->addAddress($email);
    $mail->Subject = $subject;
    $mail->Body = $body;
    $mail->send();
}

function sendAccountReopenSuccessMail($email,$username,$lang){
    $mail = new PHPMailer(true);
    mailBaseSetup($mail,$lang);

    if($lang === 'tr'){
        $subject = "uzay.info hesabınız yeniden açıldı";
        $body = "
            <p>Merhaba @{$username},</p>
            <p>Kapatılmış olan hesabınız başarıyla yeniden açıldı.</p>
            <p>Artık hesabınıza tekrar giriş yapabilirsiniz.</p>
            <p style='font-size:10px;color:#666;'>
            Bu otomatik bir e-postadır. Eğer bu işlem size ait değilse lütfen bizimle iletişime geçiniz: ".SMTP_USER."
            </p>
        ";
    } else {
        $subject = "spacepedia.info your account has been reopened";
        $body = "
            <p>Hello @{$username},</p>
            <p>Your previously closed account has been successfully reopened.</p>
            <p>You can now log in again.</p>
            <p style='font-size:10px;color:#666;'>
            This is an automated message. If this action was not performed by you, please contact us at: ".SMTP_USER."
            </p>
        ";
    }

    $mail->addAddress($email);
    $mail->Subject = $subject;
    $mail->Body = $body;
    $mail->send();
}

function formatSupportDate($dt,$lang){
    if(!$dt)return '';
    try{$d=new DateTime($dt);}catch(Exception $e){return (string)$dt;}
    if($lang==='tr')return $d->format('d.m.Y H:i');
    return $d->format('M d, Y H:i');
}

function supportFooterLine($lang){
    if($lang==='tr'){ return "Bu otomatik bir e-postadır. Eğer bu bildirimi beklemiyorduysanız, işlem başkası tarafından size hediye amaçlı sizi referans göstererek yapılmış olabilir. Bu e-postayı güvenle yok sayabilirsiniz.";
    }else{ return "This is an automated message. If you did not expect this email, the action may have been made by someone else as a gift by referencing your username. You can safely ignore this email."; }
}

function sendSupportThankYouForeverMail($email,$username){
    $profileTr='https://uzay.info/@/'.$username;
    $profileEn='https://spacepedia.info/@/'.$username;

    $mailTr=new PHPMailer(true);
    mailBaseSetup($mailTr,'tr');
    $footerTr=supportFooterLine('tr');
    $mailTr->addAddress($email);
    $mailTr->Subject="Değerli desteğiniz için çok teşekkür ederiz 💙";
    $mailTr->Body="
        <p>Merhaba @{$username},</p>
        <p>Değerli desteğiniz için çok teşekkür ederiz 💙</p>
        <p>Değerli desteğinize teşekkür amacıyla tanımlanan ikonunuz <strong>sonsuza kadar geçerlidir</strong>.</p>
        <p>Profil sayfanız: <a href='{$profileTr}'>{$profileTr}</a></p>
        <p style='font-size:10px;color:#666;'>{$footerTr}</p>
    ";
    $mailTr->send();

    $mailEn=new PHPMailer(true);
    mailBaseSetup($mailEn,'en');
    $footerEn=supportFooterLine('en');
    $mailEn->addAddress($email);
    $mailEn->Subject="Thank you so much for your valuable support 💙";
    $mailEn->Body="
        <p>Hello @{$username},</p>
        <p>Thank you so much for your valuable support 💙</p>
        <p>Your icon, assigned as a thank-you for your valuable support, is <strong>valid permanently</strong>.</p>
        <p>Here is your profile page: <a href='{$profileEn}'>{$profileEn}</a></p>
        <p style='font-size:10px;color:#666;'>{$footerEn}</p>
    ";
    $mailEn->send();
}

function sendSupportThankYouTimedMail($email,$username,$startAt,$endAt){
    $profileTr='https://uzay.info/@/'.$username;
    $profileEn='https://spacepedia.info/@/'.$username;

    $mailTr=new PHPMailer(true);
    mailBaseSetup($mailTr,'tr');
    $footerTr=supportFooterLine('tr');
    $startTr=formatSupportDate($startAt,'tr');
    $endTr=formatSupportDate($endAt,'tr');
    $mailTr->addAddress($email);
    $mailTr->Subject="Değerli desteğiniz için çok teşekkür ederiz 💙";
    $mailTr->Body="
        <p>Merhaba @{$username},</p>
        <p>Değerli desteğiniz için çok teşekkür ederiz 💙</p>
        <p>Değerli desteğinize teşekkür amaçlı verilen ikonunuz aşağıdaki tarihler arasında geçerli olacaktır:</p>
        <p><strong>Başlangıç:</strong> {$startTr}<br><strong>Bitiş:</strong> {$endTr}</p>
        <p>İkonunuzun geçerliliği sona erdiğinde, profilinizde görünmeye devam etmesini isterseniz <strong>48 saat içinde</strong> yeniden destek olabilirsiniz.</p>
        <p>Profil sayfanız: <a href='{$profileTr}'>{$profileTr}</a></p>
        <p style='font-size:10px;color:#666;'>{$footerTr}</p>
    ";
    $mailTr->send();

    $mailEn=new PHPMailer(true);
    mailBaseSetup($mailEn,'en');
    $footerEn=supportFooterLine('en');
    $startEn=formatSupportDate($startAt,'en');
    $endEn=formatSupportDate($endAt,'en');
    $mailEn->addAddress($email);
    $mailEn->Subject="Thank you so much for your valuable support 💙";
    $mailEn->Body="
        <p>Hello @{$username},</p>
        <p>Thank you so much for your valuable support 💙</p>
        <p>Your icon, given as a thank-you for your valuable support, will be valid between the following dates:</p>
        <p><strong>Start:</strong> {$startEn}<br><strong>End:</strong> {$endEn}</p>
        <p>After your icon expires, if you would like it to remain visible on your profile, you may support again within <strong>48 hours</strong>.</p>
        <p>Here is your profile page: <a href='{$profileEn}'>{$profileEn}</a></p>
        <p style='font-size:10px;color:#666;'>{$footerEn}</p>
    ";
    $mailEn->send();
}

function sendSupportExpiredReminderMail($email,$username,$removalStartHours){
    $profileTr='https://uzay.info/@/'.$username;
    $profileEn='https://spacepedia.info/@/'.$username;
    $removalEnd=$removalStartHours+24;

    $mailTr=new PHPMailer(true);
    mailBaseSetup($mailTr,'tr');
    $mailTr->addAddress($email);
    $mailTr->Subject="Değerli desteğinizin süresi doldu";
    $mailTr->Body="
        <p>Merhaba @{$username},</p>
        <p>Değerli desteğinizin süresi doldu.</p>
        <p>Değerli desteğinize teşekkür amaçlı verilen ikonunuz sistem tarafından <strong>{$removalStartHours}–{$removalEnd} saat</strong> içerisinde kaldırılabilir.</p>
        <p>İkonun devam etmesini istiyorsanız tekrar destek olabilirsiniz.</p>
        <p>Profil sayfanız: <a href='{$profileTr}'>{$profileTr}</a></p>
        <p style='font-size:10px;color:#666;'>Bu otomatik bir e-postadır. Bu e-postayı güvenle yok sayabilirsiniz.</p>
    ";
    $mailTr->send();

    $mailEn=new PHPMailer(true);
    mailBaseSetup($mailEn,'en');
    $mailEn->addAddress($email);
    $mailEn->Subject="Your valuable support has expired";
    $mailEn->Body="
        <p>Hello @{$username},</p>
        <p>Your valuable support has expired.</p>
        <p>Your icon, given as a thank-you for your valuable support, may be removed by the system within <strong>{$removalStartHours}–{$removalEnd} hours</strong>.</p>
        <p>If you want to keep your icon, you may support again.</p>
        <p>Here is your profile page: <a href='{$profileEn}'>{$profileEn}</a></p>
        <p style='font-size:10px;color:#666;'>This is an automated message. You can safely ignore this email.</p>
    ";
    $mailEn->send();
}

function sendSupportRenewedTimedMail($email,$username,$newEndAt){
    $profileTr='https://uzay.info/@/'.$username;
    $profileEn='https://spacepedia.info/@/'.$username;

    $mailTr=new PHPMailer(true);
    mailBaseSetup($mailTr,'tr');
    $footerTr=supportFooterLine('tr');
    $newEndTr=formatSupportDate($newEndAt,'tr');
    $mailTr->addAddress($email);
    $mailTr->Subject="Değerli desteğinizi yenilediğiniz için çok teşekkür ederiz 💙";
    $mailTr->Body="
        <p>Merhaba @{$username},</p>
        <p>Değerli desteğinizi yenilediğiniz için çok teşekkür ederiz 💙</p>
        <p>İkonunuzun yeni geçerlilik bitiş tarihi:</p>
        <p><strong>{$newEndTr}</strong></p>
        <p>Yeni bitiş tarihinden sonra ikonunuz kaldırılmadan önce, ikonun devam etmesini istiyorsanız <strong>48 saat içinde</strong> yeniden destek olabilirsiniz.</p>
        <p>Profil sayfanız: <a href='{$profileTr}'>{$profileTr}</a></p>
        <p style='font-size:10px;color:#666;'>{$footerTr}</p>
    ";
    $mailTr->send();

    $mailEn=new PHPMailer(true);
    mailBaseSetup($mailEn,'en');
    $footerEn=supportFooterLine('en');
    $newEndEn=formatSupportDate($newEndAt,'en');
    $mailEn->addAddress($email);
    $mailEn->Subject="Thank you so much for renewing your valuable support 💙";
    $mailEn->Body="
        <p>Hello @{$username},</p>
        <p>Thank you so much for renewing your valuable support 💙</p>
        <p>Your new icon end date is:</p>
        <p><strong>{$newEndEn}</strong></p>
        <p>After the new end date, if you want to keep your icon active before removal, you may support again within <strong>48 hours</strong>.</p>
        <p>Here is your profile page: <a href='{$profileEn}'>{$profileEn}</a></p>
        <p style='font-size:10px;color:#666;'>{$footerEn}</p>
    ";
    $mailEn->send();
}

function sendSupportRenewedUpgradedToForeverMail($email,$username){
    $profileTr='https://uzay.info/@/'.$username;
    $profileEn='https://spacepedia.info/@/'.$username;

    $mailTr=new PHPMailer(true);
    mailBaseSetup($mailTr,'tr');
    $footerTr=supportFooterLine('tr');
    $mailTr->addAddress($email);
    $mailTr->Subject="Değerli desteğinizi sonsuz geçerliliğe yükselterek desteklediğiniz için çok teşekkür ederiz 💙";
    $mailTr->Body="
        <p>Merhaba @{$username},</p>
        <p>Değerli desteğinizi sonsuz geçerliliğe yükselterek desteklediğiniz için çok teşekkür ederiz 💙</p>
        <p>İkonunuz artık <strong>sonsuz geçerlilikle</strong> aktif olacaktır.</p>
        <p>Profil sayfanız: <a href='{$profileTr}'>{$profileTr}</a></p>
        <p style='font-size:10px;color:#666;'>{$footerTr}</p>
    ";
    $mailTr->send();

    $mailEn=new PHPMailer(true);
    mailBaseSetup($mailEn,'en');
    $footerEn=supportFooterLine('en');
    $mailEn->addAddress($email);
    $mailEn->Subject="Thank you so much for upgrading your valuable support to permanent validity 💙";
    $mailEn->Body="
        <p>Hello @{$username},</p>
        <p>Thank you so much for upgrading your valuable support to permanent validity 💙</p>
        <p>Your icon is now activated with <strong>permanent validity</strong>.</p>
        <p>Here is your profile page: <a href='{$profileEn}'>{$profileEn}</a></p>
        <p style='font-size:10px;color:#666;'>{$footerEn}</p>
    ";
    $mailEn->send();
}
?>