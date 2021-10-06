<?php
 namespace MailPoetVendor; if (!defined('ABSPATH')) exit; class Swift_Plugins_ImpersonatePlugin implements \MailPoetVendor\Swift_Events_SendListener { private $sender; public function __construct($sender) { $this->sender = $sender; } public function beforeSendPerformed(\MailPoetVendor\Swift_Events_SendEvent $evt) { $message = $evt->getMessage(); $headers = $message->getHeaders(); $headers->addPathHeader('X-Swift-Return-Path', $message->getReturnPath()); $message->setReturnPath($this->sender); } public function sendPerformed(\MailPoetVendor\Swift_Events_SendEvent $evt) { $message = $evt->getMessage(); $headers = $message->getHeaders(); if ($headers->has('X-Swift-Return-Path')) { $message->setReturnPath($headers->get('X-Swift-Return-Path')->getAddress()); $headers->removeAll('X-Swift-Return-Path'); } } } 