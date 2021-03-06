<?php

namespace Drupal\membership_offer\Entity;

use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\Entity\EntityChangedTrait;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\membership\EventDispatcherTrait;
use Drupal\membership_commerce\Entity\PurchasableMembershipBase;
use Drupal\membership_offer\MembershipOfferInterface;
use Drupal\user\UserInterface;

/**
 * Defines the Membership Offer entity.
 *
 * @ingroup membership
 *
 * @ContentEntityType(
 *   id = "membership_offer",
 *   label = @Translation("Membership Offer"),
 *   handlers = {
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\membership_offer\MembershipOfferListBuilder",
 *     "views_data" = "Drupal\membership_offer\Entity\MembershipOfferViewsData",
 *     "form" = {
 *       "default" = "Drupal\membership_offer\Form\MembershipOfferForm",
 *       "add" = "Drupal\membership_offer\Form\MembershipOfferForm",
 *       "edit" = "Drupal\membership_offer\Form\MembershipOfferForm",
 *       "delete" = "Drupal\membership_offer\Form\MembershipOfferDeleteForm",
 *     },
 *     "access" = "Drupal\membership_offer\MembershipOfferAccessControlHandler",
 *     "route_provider" = {
 *       "html" = "Drupal\membership_offer\MembershipOfferHtmlRouteProvider",
 *     },
 *   },
 *   base_table = "membership_offer",
 *   admin_permission = "administer membership offer entities",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "name",
 *     "uuid" = "uuid",
 *     "uid" = "user_id",
 *     "langcode" = "langcode",
 *     "status" = "status",
 *   },
 *   links = {
 *     "canonical" = "/membership_offer/{membership_offer}",
 *     "add-form" = "/admin/structure/membership/offer/add",
 *     "edit-form" = "/admin/structure/membership/offer/{membership_offer}/edit",
 *     "delete-form" = "/admin/structure/membership/offer/{membership_offer}/delete",
 *     "collection" = "/admin/structure/membership/offer",
 *   },
 *   field_ui_base_route = "membership_offer.settings"
 * )
 */
class MembershipOffer extends PurchasableMembershipBase implements MembershipOfferInterface {

  use EntityChangedTrait;
  use EventDispatcherTrait;

  /**
   * {@inheritdoc}
   */
  public static function preCreate(EntityStorageInterface $storage_controller, array &$values) {
    parent::preCreate($storage_controller, $values);
    $values += array(
      'user_id' => \Drupal::currentUser()->id(),
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getName() {
    return $this->get('name')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setName($name) {
    $this->set('name', $name);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getCreatedTime() {
    return $this->get('created')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setCreatedTime($timestamp) {
    $this->set('created', $timestamp);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getOwner() {
    return $this->get('user_id')->entity;
  }

  /**
   * {@inheritdoc}
   */
  public function getOwnerId() {
    return $this->get('user_id')->target_id;
  }

  /**
   * {@inheritdoc}
   */
  public function setOwnerId($uid) {
    $this->set('user_id', $uid);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function setOwner(UserInterface $account) {
    $this->set('user_id', $account->id());
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function isPublished() {
    return (bool) $this->getEntityKey('status');
  }

  /**
   * {@inheritdoc}
   */
  public function setPublished($published) {
    $this->set('status', $published ? TRUE : FALSE);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {
    $fields['id'] = BaseFieldDefinition::create('integer')
      ->setLabel(t('ID'))
      ->setDescription(t('The ID of the Membership Offer entity.'))
      ->setReadOnly(TRUE);
    $fields['uuid'] = BaseFieldDefinition::create('uuid')
      ->setLabel(t('UUID'))
      ->setDescription(t('The UUID of the Membership Offer entity.'))
      ->setReadOnly(TRUE);

    $fields['user_id'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Authored by'))
      ->setDescription(t('The user ID of author of the Membership Offer entity.'))
      ->setRevisionable(TRUE)
      ->setSetting('target_type', 'user')
      ->setSetting('handler', 'default')
      ->setDefaultValueCallback('Drupal\node\Entity\Node::getCurrentUserId')
      ->setTranslatable(TRUE)
      ->setDisplayOptions('view', array(
        'label' => 'hidden',
        'type' => 'author',
        'weight' => 0,
      ))
      ->setDisplayOptions('form', array(
        'type' => 'entity_reference_autocomplete',
        'weight' => 5,
        'settings' => array(
          'match_operator' => 'CONTAINS',
          'size' => '60',
          'autocomplete_type' => 'tags',
          'placeholder' => '',
        ),
      ))
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['name'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Name'))
      ->setDescription(t('The name of the Membership Offer entity.'))
      ->setSettings(array(
        'max_length' => 50,
        'text_processing' => 0,
      ))
      ->setDefaultValue('')
      ->setDisplayOptions('view', array(
        'label' => 'above',
        'type' => 'string',
        'weight' => -4,
      ))
      ->setDisplayOptions('form', array(
        'type' => 'string_textfield',
        'weight' => -4,
      ))
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['status'] = BaseFieldDefinition::create('boolean')
      ->setLabel(t('Publishing status'))
      ->setDescription(t('A boolean indicating whether the Membership Offer is published.'))
      ->setDefaultValue(TRUE);

    $fields['langcode'] = BaseFieldDefinition::create('language')
      ->setLabel(t('Language code'))
      ->setDescription(t('The language code for the Membership Offer entity.'))
      ->setDisplayOptions('form', array(
        'type' => 'language_select',
        'weight' => 10,
      ))
      ->setDisplayConfigurable('form', TRUE);

    $fields['created'] = BaseFieldDefinition::create('created')
      ->setLabel(t('Created'))
      ->setDescription(t('The time that the entity was created.'));

    $fields['changed'] = BaseFieldDefinition::create('changed')
      ->setLabel(t('Changed'))
      ->setDescription(t('The time that the entity was last edited.'));

    $fields['membership_type'] = BaseFieldDefinition::create('entity_reference')
      ->setCardinality(1)
      ->setLabel(t('Membership Type'))
      ->setRequired(TRUE)
      ->setDescription(t('The Membership type that is created as a result of redeeming this offer.'))
      ->setRevisionable(TRUE)
      ->setSetting('target_type', 'membership_type')
      ->setTranslatable(TRUE)
      ->setDisplayOptions('form', array(
        'type' => 'options_select',
        'weight' => 1,
      ))
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);
    $fields['order_item_type'] = BaseFieldDefinition::create('entity_reference')
      ->setRequired(TRUE)
      ->setCardinality(1)
      ->setLabel(t('Order Item Type'))
      ->setDescription(t('The Order Item type this membership offer creates.'))
      ->setRevisionable(TRUE)
      ->setSetting('target_type', 'commerce_order_item_type')
      ->setTranslatable(TRUE)
      ->setDisplayOptions('form', array(
        'type' => 'options_select',
        'weight' => 1,
      ))
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);
    $fields['stores'] = BaseFieldDefinition::create('entity_reference')
      ->setCardinality(FieldStorageDefinitionInterface::CARDINALITY_UNLIMITED)
      ->setRequired(TRUE)
      ->setLabel(t('Stores'))
      ->setDescription(t('The Stores from which this membership offer may be purchased.'))
      ->setRevisionable(TRUE)
      ->setSetting('target_type', 'commerce_store')
      ->setTranslatable(TRUE)
      ->setDisplayOptions('form', array(
        'type' => 'options_select',
        'weight' => 1,
      ))
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);
    $fields['path'] = BaseFieldDefinition::create('path')
      ->setLabel(t('URL alias'))
      ->setDescription(t('The product URL alias.'))
      ->setTranslatable(TRUE)
      ->setDisplayOptions('form', [
        'type' => 'path',
        'weight' => 30,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setComputed(TRUE);

    return $fields;
  }

  /**
   * @inheritDoc
   */
  public function getStores() {
    return $this->get('stores')->referencedEntities();
  }

  /**
   * @inheritDoc
   */
  public function getPrice() {
    $fields = $this->getFieldDefinitions();
    $price = [];
    /** @var \Drupal\Core\Field\FieldDefinitionInterface $field */
    foreach ($fields as $field) {
      if ($field->getType() == 'commerce_price') {
        $price[] = $field;
      }
    }
    if (count($price) === 1) {
      /** @var \Drupal\Core\Field\FieldItemListInterface $field */
      $field = $this->get(reset($price)->getName());
      // Return the first price value, could perhaps be multi-valued in the case of recurring pricing.
      return $field->first()->toPrice();
    }
    return NULL;
  }

  /**
   * @inheritDoc
   */
  public function getOrderItemTypeId() {
    return $this->get('order_item_type')->getString();
  }

  /**
   * @inheritDoc
   */
  public function getOrderItemTitle() {
    return $this->label();
  }

  /**
   * @inheritDoc
   */
  public function getMembershipTypeId() {
    return $this->get('membership_type')->getString();
  }

  /**
   * @inheritDoc
   */
  public function createMembership() {
    $membership = parent::createMembership();
    $membership->set('membership_offer', ['target_id' => $this->id()]);
    return $membership;
  }

}
