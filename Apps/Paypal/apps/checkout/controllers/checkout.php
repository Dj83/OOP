<?php

defined('_JEXEC') or jexit('Restricted Access');

class CheckoutControllerCheckout extends JController
{
	public function display($cachable = false, $urlparams = false)
	{
		$app = JFactory::getApplication();
		$vName		= $app->input->get('view', 'checkout');
		JRequest::setVar('view', $vName);

		$model = $this->createModel('Cart', 'CheckoutModel', array('ignore_request'=>true));

			//$model->setState('cart.id', $id);

		// Prepare the checkout button
		jimport('paypal.express.checkout');
		$paypal = new PayPalCheckout($model->getUrls());
		$model->setState('cart.apitoken', $paypal->get('token'));

		return parent::display($cachable, array('Itemid'=>'INT'));
	}
	/** 
	 * This method gets called by paypal
	 * it is basically the same data we sent 
	 * except it tells us we're done...
	 * we can now checkout the cart items from the session && d.b.
	 **/
	public function paypal()
	{
		// Check the request for forgeries
		
	}

	private function _sendEmail($data, $contact)
	{
			$app		= JFactory::getApplication();
			$params 	= JComponentHelper::getParams('contact');
			if ($contact->email_to == '' && $contact->user_id != 0) {
				$contact_user = JUser::getInstance($contact->user_id);
				$contact->email_to = $contact_user->get('email');
			}
			$mailfrom	= $app->getCfg('mailfrom');
			$fromname	= $app->getCfg('fromname');
			$sitename	= $app->getCfg('sitename');
			$copytext 	= JText::sprintf('COM_CONTACT_COPYTEXT_OF', $contact->name, $sitename);

			$name		= $data['contact_name'];
			$email		= $data['contact_email'];
			$subject	= $data['contact_subject'];
			$body		= $data['contact_message'];

			// Prepare email body
			$prefix = JText::sprintf('COM_CONTACT_ENQUIRY_TEXT', JURI::base());
			$body	= $prefix."\n".$name.' <'.$email.'>'."\r\n\r\n".stripslashes($body);

			$mail = JFactory::getMailer();
			$mail->addRecipient($contact->email_to);
			$mail->addReplyTo(array($email, $name));
			$mail->setSender(array($mailfrom, $fromname));
			$mail->setSubject($sitename.': '.$subject);
			$mail->setBody($body);
			$sent = $mail->Send();

			//If we are supposed to copy the sender, do so.

			// check whether email copy function activated
			if ( array_key_exists('contact_email_copy', $data)  ) {
				$copytext		= JText::sprintf('COM_CONTACT_COPYTEXT_OF', $contact->name, $sitename);
				$copytext		.= "\r\n\r\n".$body;
				$copysubject	= JText::sprintf('COM_CONTACT_COPYSUBJECT_OF', $subject);

				$mail = JFactory::getMailer();
				$mail->addRecipient($email);
				$mail->addReplyTo(array($email, $name));
				$mail->setSender(array($mailfrom, $fromname));
				$mail->setSubject($copysubject);
				$mail->setBody($copytext);
				$sent = $mail->Send();
			}

			return $sent;
	}
}