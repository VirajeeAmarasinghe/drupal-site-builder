<?php
/**
 * @file
 * Contains Drupal\dictionary\Form\TermForm
 */
namespace Drupal\dictionary\Form;

use Drupal\Core\Entity\ContentEntityForm;
use Drupal\Core\Form\FormStateInterface;

/**
 * Form controller for the content_entity_example entity edit forms
 * 
 * @ingroup content_entity_example
 */
class TermForm extends ContentEntityForm{
    /**
     * {@inheritdoc}
     */
    public function buildForm(array $form, FormStateInterface $form_state){
        $form = parent::buildForm($form, $form_state);
        return $form;
    }

    /**
     * {@inheritdoc}
     * 
     * @param array $form
     * @param FormStateInterface $form_state
     * @return void
     */
    public function save(array $form, FormStateInterface $form_state){
        $form_state->setRedirect('entity.dictionary_term.collection');
        $entity = $this->getEntity();
        $entity->save();
    }
}
?>