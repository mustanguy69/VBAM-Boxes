<?php

namespace Appscore\BranchLocator\Block\Adminhtml\Branchlist\Edit\Tab;

use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Backend\Block\Widget\Tab\TabInterface;
use Magento\Backend\Block\Template\Context;
use Magento\Framework\Registry;
use Magento\Framework\Data\FormFactory;
use Magento\Cms\Model\Wysiwyg\Config;
use Appscore\BranchLocator\Model\System\Config\Status;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Theme\Block\Html\Header\Logo;

class Info extends Generic implements TabInterface
{
    /**
     * @var \Magento\Cms\Model\Wysiwyg\Config
     */
    protected $_wysiwygConfig;

    /**
     * @var \BranchLocator\Model\System\Config\Status
     */
    protected $_branchlistStatus;

    protected $_logo;

   /**
     * @param Context $context
     * @param Registry $registry
     * @param FormFactory $formFactory
     * @param Config $wysiwygConfig
     * @param Status $newsStatus
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        FormFactory $formFactory,
        Config $wysiwygConfig,
        Status $branchlistStatus,
        Logo $logo,
        array $data = []
    ) {
        $this->_wysiwygConfig = $wysiwygConfig;
        $this->_branchlistStatus = $branchlistStatus;
        $this->_logo = $logo;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * Prepare form fields
     *
     * @return \Magento\Backend\Block\Widget\Form
     */
    protected function _prepareForm()
    {
        $model = $this->_coreRegistry->registry('branchlocator_branchlist');
    
        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('branch_');
        $form->setFieldNameSuffix('branch');

        $fieldset = $form->addFieldset(
            'base_fieldset',
            ['legend' => __('General')]
        );

        if ($model->getId()) {
            $fieldset->addField(
                'id',
                'hidden',
                ['name' => 'id']
            );
        }

        $fieldset->addField(
            'status',
            'select',
            [
                'name'      => 'status',
                'label'     => __('Status'),
                'options'   => $this->_branchlistStatus->toOptionArray()
            ]
        );

        $fieldset->addField(
            'name',
            'text',
            [
                'name'        => 'name',
                'label'    => __('Name'),
                'required'     => true
            ]
        );

        $fieldset->addField(
            'address',
            'text',
            [
                'name'        => 'address',
                'label'    => __('Address'),
                'required'     => true
            ]
        );

        $fieldset->addField(
            'city',
            'text',
            [
                'name'        => 'city',
                'label'    => __('City'),
                'required'     => true
            ]
        );

        $country = $fieldset->addField(
            'country',
            'select',
            [
                'name'        => 'country',
                'label'    => __('Country'),
                'required'     => true,
                'value' => 'AUS',
                "values"    =>      [
                    ["value" => "AU","label" => __("Australia")],
                    ["value" => "NZ","label" => __("New Zealand")],
                ]
            ]
        );

        $state = $fieldset->addField(
            'state',
            'select',
            [
                'name'        => 'state',
                'label'    => __('State'),
                'required'     => true,
                "values"    =>      [
                    ["value" => "NSW","label" => __("New South Wales")],
                    ["value" => "QLD","label" => __("Queensland")],
                    ["value" => "SA","label" => __("South Australia")],
                    ["value" => "TAS","label" => __("Tasmania")],
                    ["value" => "ACT","label" => __("Australian Capital Territory")],
                    ["value" => "VIC","label" => __("Victoria")],
                    ["value" => "WA","label" => __("Western Australia")],
                ]
            ]
        );

        $fieldset->addField(
            'postcode',
            'text',
            [
                'name'        => 'postcode',
                'label'    => __('Postcode'),
                'required'     => true
            ]
        );

        $fieldset->addField(
            'phone',
            'text',
            [
                'name'        => 'phone',
                'label'    => __('Phone'),
                'required'     => true
            ]
        );

        $fieldset->addField(
            'fax',
            'text',
            [
                'name'        => 'fax',
                'label'    => __('Fax'),
                'required'     => false
            ]
        );

        $fieldset->addField(
            'latitude',
            'text',
            [
                'name'        => 'latitude',
                'label'    => __('Latitude'),
                'required'     => false
            ]
        );

        $longitude = $fieldset->addField(
            'longitude',
            'text',
            [
                'name'        => 'longitude',
                'label'    => __('Longitude'),
                'required'     => false
            ]
        );



        $state->setAfterElementHtml('<script>
        //<![CDATA[
            (function() {
                
                var countrySelect = document.getElementById("branch_country");
                var stateSelect = document.getElementById("branch_state");

                var ausState = {
                    "NSW": "New South Wales",
                    "QLD": "Queensland",
                    "SA": "South Australia",
                    "TAS": "Tasmania",
                    "ACT": "Australian Capital Territory",
                    "VIC": "Victoria",
                    "WA": "Western Australia",
                };

                countrySelect.onchange = function(e) {
                    if(countrySelect.options[countrySelect.selectedIndex].value == "AUS") {
                        removeOptions(stateSelect);
                        for (var key in ausState) {
                            var opt = document.createElement("option");
                            opt.value = key;
                            opt.innerHTML = ausState[key];
                            stateSelect.add(opt);
                        }
                        
                    }

                    if(countrySelect.options[countrySelect.selectedIndex].value == "NZ") {
                        removeOptions(stateSelect);
                        var opt = document.createElement("option");
                        opt.value = "NZ";
                        opt.innerHTML = "New Zealand";
                        stateSelect.add(opt);

                        stateSelect.value = "NZ";
                        
                    }
                };

                function removeOptions(selectElement) {
                    var i, L = selectElement.options.length - 1;
                    for(i = L; i >= 0; i--) {
                       selectElement.remove(i);
                    }
                 }

            })(); 
        //]]>
        </script>');


        $longitude->setAfterElementHtml('<script>
        //<![CDATA[
            function initMap() {
                lat = "'.$model->getLatitude().'";
                lng = "'.$model->getLongitude().'";

                if(lat !== "" && lng !== "") {
                    defaultPosition = {lat: lat, lng: lng}
                } else {
                    defaultPosition = {lat: -37.8867462, lng: 144.841503}
                }
                map = new google.maps.Map(document.getElementById("map"), {
                    center: defaultPosition,
                    zoom: 7,
                });

                var logo = {
                    url: "'.$this->getLogoSrc().'",
                    scaledSize: new google.maps.Size(60, 40),
                };
                marker = new google.maps.Marker({
                    position: defaultPosition,
                    map: map,
                    icon: logo,
                });


                geocoder = new google.maps.Geocoder();
        
            }

            function placeMarker(position) {
                marker.setPosition(position);
            }
        

            function geocodeAddress(geocoder, resultsMap, address) {
                geocoder.geocode({"address": address}, function(results, status) {
                    resultsMap.setCenter(results[0].geometry.location);
                    if (status === "OK") {
                        var lat = document.getElementById("branch_latitude");
                        var lng = document.getElementById("branch_longitude");
                        lat.value = results[0].geometry.location.lat();
                        lng.value = results[0].geometry.location.lng();
                        console.log(results[0].geometry.location)
                        placeMarker(results[0].geometry.location)
                    } else {
                        alert("Geocode was not successful for the following reason: " + status);
                    }
                });
            }

            (function() {
                
                var address = document.getElementById("branch_address");
                var city = document.getElementById("branch_city");
                var postcode = document.getElementById("branch_postcode")
                var countrySelect = document.getElementById("branch_country");
                var stateSelect = document.getElementById("branch_state");

                address.addEventListener("change",function () {
                    geocode();
                });

                city.addEventListener("change",function () {
                    geocode();
                });

                postcode.addEventListener("change",function () {
                    geocode();
                });

                countrySelect.addEventListener("change",function () {
                    geocode();
                });

                stateSelect.addEventListener("change",function () {
                    geocode();
                });

                function geocode() {
                    geocodeAddress(geocoder, map, ""+ address.value+ " "+ city.value + " "+ postcode.value + " "+ countrySelect.value + " "+ stateSelect.value +"");
                }

            })(); 
        //]]>
        </script>
        <script src="https://maps.googleapis.com/maps/api/js?key='. $this->getApiKey() .'&callback=initMap" async defer></script>
        <div id="map" style="width:500px; height:400px; margin-top:40px;">
        ');

        $form->setValues($model->getData());
        $this->setForm($form);

        return parent::_prepareForm();
    }

    public function getApiKey()
	{
		$scope = ScopeConfigInterface::SCOPE_TYPE_DEFAULT;
		
		return $this->_scopeConfig->getValue('branchlocator/general/apiKey', $scope);
    }
    
    public function getLogoSrc()
    {    
        return $this->_logo->getLogoSrc();
    }

    /**
     * Prepare label for tab
     *
     * @return string
     */
    public function getTabLabel()
    {
        return __('Branch Info');
    }
 
    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle()
    {
        return __('Branch Info');
    }
 
    /**
     * {@inheritdoc}
     */
    public function canShowTab()
    {
        return true;
    }
 
    /**
     * {@inheritdoc}
     */
    public function isHidden()
    {
        return false;
    }
}