<?php
/**
 * @file  
 * Contains \Drupal\rsvplist\Form\RSVPSettingsForm
 */

 namespace Drupal\rsvplist\Form;

 use Drupal\Core\Form\ConfigFormBase;
 use Symfony\Component\HttpFoundation\Request;
 use Drupal\Core\Form\FormstateInterface;
 use Symfony\Component\DependencyInjection\ContainerInterface;
 use Drupal\Core\Entity\EntityTypeManagerInterface;
 use Drupal\Core\State\StateInterface;   

 /**
  * Defines a form to configure RSVP List module settings
  */

  class RSVPSettingsForm extends ConfigFormBase {
      /**
       * The entity field manager
       * @var \Drupal\Core\Entity\EntityFieldManagerInterface
       */
      protected $entityFieldManager;

      public function __construct(EntityTypeManagerInterface $entityTypeManager){
        $this->entityTypeManager = $entityTypeManager;
      }

      /**
       * {@inheritDoc}
       */
      public static function create(ContainerInterface $container){
        return new static(
            $container->get('entity_type.manager')
        );
      }

      /**
       * {@inheritDoc}
       */
      public function getFormID(){
          return "rsvplist_admin_settings";
      }

      /**
       * {@inheritDoc}
       * return the name of the settings file we are going to create
       */
      protected function getEditableConfigNames(){
          return [
              'rsvplist.settings'
          ];
      }

      /**
       * {@inheritDoc}       
       */
      public function buildForm(array $form, FormStateInterface $form_state, Request $request = NULL){
        $types = $this->getExistingNodeTypes(); 
        $config = $this->config('rsvplist.settings'); 
        $form['rsvplist_types'] = array(
            '#type' => 'checkboxes',
            '#title' => $this->t('The content types to enable RSVP collection for'),
            '#default_value' => $config->get('allowed_types'),
            '#options' => $types,
            '#description' => t('On the specified node types, an RSVP option will be available and can be enabled while that node is being edited.')
        );
        $form['array_filter'] = array(
            '#type' => 'value',
            '#value' => TRUE
        );
        return parent::buildForm($form, $form_state);
      }

      /**
       * {@inheritDoc}
       */
      public function submitForm(array &$form, FormStateInterface $form_state){
          $allowed_types = array_filter($form_state->getValue('rsvplist_types')); 
          sort($allowed_types);
          $this->config('rsvplist.settings')
          ->set('allowed_types', $allowed_types)
          ->save();

          parent::submitForm($form, $form_state);
      }

      /**
       * Returns a list of all the node types currently installed.
       *
       * @return array
       * An array of node types
       */
      public function getExistingNodeTypes(){
        $types = [];        

        $nodeTypes = $this->entityTypeManager->getStorage('node_type')->loadMultiple(); 
        foreach($nodeTypes as $nodeType){ 
            $types[$nodeType->id()] = $nodeType->label();
        } 
        return $types;
      }
  }