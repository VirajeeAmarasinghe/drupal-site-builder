<?php 
/**
 * @file 
 * Contains \Drupal\rsvplist\Form\RSVPForm
 */
namespace Drupal\rsvplist\Form; 

use Drupal\Core\Database\Database;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Provides an RSVP Email form
 */
class RSVPForm extends FormBase{
    /**
     * (@inheritDoc)
     */
    public function getFormId(){
        return "rsvplist_email_form";
    }

    /**
     * (@inheritDoc)
     */
    public function buildForm(array $form, FormStateInterface $form_state){
        $node = \Drupal::routeMatch()->getParameter('node'); 
        $nid = 0;
        if($node){
            $nid = $node->nid->value;
        }
        
        $form['email'] = array(
            '#title' => t('Email Address'),
            '#type' => 'textfield',
            '#size' => 25,
            '#description' => t("We'll send updates to the email address you provide."),
            '#required' => TRUE
        );
        $form['submit'] = array(
            '#type' => 'submit',
            '#value' => t('RSVP')
        );
        $form['nid'] = array(
            '#type' => 'hidden',
            '#value' => $nid
        );
        return $form;
    }

    /**
     * (@inheritDoc)
     */
    public function validateForm(array &$form, FormStateInterface $form_state){
        $value = $form_state->getValue('email'); 
                
        if(!\Drupal::service('email.validator')->isValid($value)){
            $form_state->setErrorByName('email', t('The email address %mail is not valid.', 
            array('%mail' => $value)));
            return;
        }

        $node = \Drupal::routeMatch()->getParameter('node');
        // Check if email already is set for this node
        $select = Database::getConnection()->select('rsvplist', 'r');
        $select->fields('r', array('nid'));
        $select->condition('nid', $node->id());
        $select->condition('mail', $value);
        $results = $select->execute();

        if(!empty($results->fetchCol())){
            //We found a row with this nid and email
            $form_state->setErrorByName('email', t('The address %mail is already subscribed to this list.', array('%mail' => $value)));
        }
    }

    public function submitForm(array &$form, FormStateInterface $form_state){
        $fields = $form_state->getValues();

        $email = $fields['email'];
        $nid = $fields['nid'];
        $uid = \Drupal::currentUser()->id();
        $created = time();

        $fields = ['mail'=>$email, 'nid'=>$nid, 'uid'=>$uid, 'created'=>$created];
        $query = \Drupal::database();
        $query->insert('rsvplist')->fields($fields)->execute();

        \Drupal::messenger()->addMessage(t('Thank you for your RSVP, you are on the list for the event.'));        
    }
}