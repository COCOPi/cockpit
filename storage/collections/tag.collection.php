<?php
 return array (
  'name' => 'tag',
  'label' => 'Tag',
  '_id' => 'tag',
  'fields' => 
  array (
    0 => 
    array (
      'name' => 'name',
      'label' => '',
      'type' => 'text',
      'default' => '',
      'info' => '',
      'group' => '',
      'localize' => false,
      'options' => 
      array (
      ),
      'width' => '1-1',
      'lst' => true,
      'acl' => 
      array (
      ),
      'required' => true,
    ),
  ),
  'sortable' => false,
  'in_menu' => false,
  '_created' => 1621752765,
  '_modified' => 1621757910,
  'color' => '',
  'acl' => 
  array (
    'publisher' => 
    array (
      'collection_edit' => false,
      'entries_view' => true,
      'entries_edit' => true,
      'entries_create' => true,
      'entries_delete' => true,
    ),
    'user' => 
    array (
      'entries_view' => true,
    ),
  ),
  'sort' => 
  array (
    'column' => '_created',
    'dir' => -1,
  ),
  'rules' => 
  array (
    'create' => 
    array (
      'enabled' => false,
    ),
    'read' => 
    array (
      'enabled' => false,
    ),
    'update' => 
    array (
      'enabled' => false,
    ),
    'delete' => 
    array (
      'enabled' => false,
    ),
  ),
  'icon' => 'tickets.svg',
);