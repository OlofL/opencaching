<?php

require_once $_SERVER['DOCUMENT_ROOT'] . '/lib2/error.inc.php';

class ChildWp_Presenter
{
  const req_cache_id = 'cacheid';
  const req_wp_type = 'wp_type';
  const req_wp_desc = 'desc';
  const tpl_page_title = 'pagetitle';
  const tpl_wp_type = 'wpType';
  const tpl_wp_desc = 'wpDesc';
  const tpl_wp_type_ids = 'wpTypeIds';
  const tpl_wp_type_names = 'wpTypeNames';
  const tpl_wp_type_error = 'wpTypeError';

  private $request;
  private $translator;
  private $coordinate;
  private $waypointTypes = array();
  private $waypointTypeValid = true;

  public function __construct($request = false, $translator = false)
  {
    $this->request = $this->initRequest($request);
    $this->translator = $this->initTranslator($translator);
    $this->coordinate = new Coordinate_Presenter($this->request, $this->translator);
  }

  private function initRequest($request)
  {
    if ($request)
      return $request;

    return new Http_Request();
  }

  private function initTranslator($translator)
  {
    if ($translator)
      return $translator;

    return new Language_Translator();
  }

  public function addWaypoint($childWpHandler)
  {
    $coordinate = $this->coordinate->getCoordinate();
    $description = htmlspecialchars($this->getDesc(), ENT_COMPAT, 'UTF-8');

    $childWpHandler->add($this->getCacheId(), $this->getType(), $coordinate->latitude(), $coordinate->longitude(), $description);
  }

  private function getCacheId()
  {
    return $this->request->get(self::req_cache_id);
  }

  private function getType()
  {
    return $this->request->get(self::req_wp_type, '0');
  }

  private function getDesc()
  {
    return $this->request->get(self::req_wp_desc);
  }

  public function init($template, $cacheManager)
  {
    $cacheid = $this->request->getForValidation(self::req_cache_id);

    if (!$cacheManager->exists($cacheid) || !$cacheManager->userMayModify($cacheid))
      $template->error(ERROR_CACHE_NOT_EXISTS);
  }

  public function prepare($template)
  {
    $template->assign(self::tpl_page_title, $this->translator->Translate('Add waypoint'));
    $template->assign(self::tpl_wp_desc, $this->getDesc());
    $template->assign(self::tpl_wp_type, $this->getType());
    $this->prepareTypes($template);
    $this->coordinate->prepare($template);

    if (!$this->waypointTypeValid)
      $template->assign(self::tpl_wp_type_error, $this->translator->translate('Select waypoint type'));
  }

  private function prepareTypes($template)
  {
    $template->assign(self::tpl_wp_type_ids, array_keys($this->waypointTypes));
    $template->assign(self::tpl_wp_type_names, $this->waypointTypes);
  }

  public function setTypes($waypointTypes)
  {
    $this->waypointTypes = array();

    foreach ($waypointTypes as $type)
    {
      $this->waypointTypes[$type->getId()] = $this->translator->translate($type->getName());
    }
  }

  public function validate()
  {
    $wpTypeValidator = new Validator_Array(array_keys($this->waypointTypes));

    $this->request->validate(self::req_wp_desc, new Validator_AlwaysValid());
    $this->waypointTypeValid = $this->request->validate(self::req_wp_type, $wpTypeValidator);
    $coordinateValid = $this->coordinate->validate();

    return $this->waypointTypeValid && $coordinateValid;
  }
}

?>
