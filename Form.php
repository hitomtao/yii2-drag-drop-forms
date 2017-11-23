<?php

namespace masihfathi\form;

use Yii;
use yii\base\Widget;
use yii\base\DynamicModel;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use masihfathi\form\FormBase;
use masihfathi\form\models\FormModel;
use masihfathi\form\widgets\email\Send as SendEmail;

/**
 * FormRender: Render form
 * Two method render form : php or js (beta)
 *
 * @author Rafal Marguzewicz <info@pceuropa.net>
 * @version 1.4
 * @license MIT
 *
 * https://github.com/pceuropa/yii2-forum
 * Please report all issues at GitHub
 * https://github.com/pceuropa/yii2-forum/issues
 *
 * Usage example:
 * ~~~
 * echo \pceuropa\forms\form::widget([
 *    'form' => ',{}'
 * ]);
 *
 * echo \pceuropa\forms\form::widget([
 *    'formId' => 1,
 * ]);
 * ~~~
 * echo \pceuropa\forms\Form::widget([
 * FormBuilder requires Yii 2
 * http://www.yiiframework.com
 * https://github.com/yiisoft/yii2
 *
 */
class Form extends Widget {

    /**
     * @var int Id of form. If set, widget take data from FormModel.
     * @see \masihfathi\form\models\FormModel
     */
    public $formId = null;

    /**
     * @var array|string JSON Object representing the form body
     */
    public $body = '{}';

    /**
     * @var string Type render js|php
     * @since 1.0
     */
    public $typeRender = 'php';

    /**
     * @var integer item id of form
     */
    public $itemId = null;
    /**
     * Initializes the object.
     *
     * @return void
     * @see Widget
     */
    public function init() {
        parent::init();
        if (is_int($this->formId)) {
            $form = FormModel::FindOne($this->formId);
            $this->body = $form->body;
        }

        $this->body = Json::decode($this->body);
    }

    /**
     * Executes the widget.
     * @since 1.0
     * @return function
     */
    public function run() {
        if ($this->typeRender === 'js') {
            return $this->jsRender($this->body);
        }
        return $this->phpRender($this->body);
    }

    /**
     * Create form
     * Render form by PHP language.
     * @param array $form
     * @return View Form
     */
    public function phpRender($form) {

        $data_fields = FormBase::onlyCorrectDataFields($form);
        $DynamicModel = new DynamicModel(ArrayHelper::getColumn($data_fields, 'name'));

        foreach ($data_fields as $v) {

            if (isset($v["name"]) && $v["name"]) {

                if (isset($v["require"]) && $v["require"]) {
                    $DynamicModel->addRule($v["name"], 'required');
                }

                $DynamicModel->addRule($v["name"], FormBase::ruleType($v));
            }
        }
        if (is_null($this->itemId)) {
            return $this->render('form_php', [
                'form_body' => $form,
                'model' => $DynamicModel
            ]);
        }
        return $this->render('form_php', [
                'itemId' => $this->itemId,
                'form_body' => $form,
                'model' => $DynamicModel
        ]);
    }

    /**
     * Create form
     * Render form by JavaScript language.
     * @param array $form
     * @return View
     */
    public function jsRender($form) {
        return $this->render('form_js', ['form' => $form]);
    }

    /**
     * Select and return function render field
     * Render field in view
     * @param yii\bootstrap\ActiveForm $form
     * @param DynamicModel $model
     * @param array $field
     * @return string Return field in div HTML
     */
    public static function field($form, $model, $field = null) {

        $width = $field['width'];

        switch ($field['field']) {
            case 'input':
                $field = self::input($form, $model, $field);
                break;
            case 'textarea':
                $field = self::textarea($form, $model, $field);
                break;
            case 'radio':
                $field = self::radio($form, $model, $field);
                break;
            case 'checkbox':
                $field = self::checkbox($form, $model, $field);
                break;
            case 'select':
                $field = self::select($form, $model, $field);
                break;
            case 'description':
                $field = self::description($field);
                break;
            case 'submit':
                $field = self::submit($field);
                break;
            default:
                $field = '';
                break;
        }

        return self::div($width, $field);
    }

    /**
     * Return HTML div with field
     * @param string $width Class bootstrap
     * @param string $field
     * @return string
     */
    public static function div($width, $field) {
        return '<div class="' . $width . '">' . $field . '</div>';
    }

    /**
     * Title description
     * @param string $arg
     * @return void
     */
    public function mergeOptions($options, $field) {

        foreach (['placeholder', 'value', 'id', 'class'] as $key => $value) {
            if (isset($field[$value])) {
                $options[$value] = $field[$value];
            }
        }
    }

    /**
     * Renders an input tag
     * @param yii\bootstrap\ActiveForm $form
     * @param DynamicModel $model
     * @param array $_data
     * @return this The field object itself.
     */
    public function input($form, $model, $field) {

        $options = [];
        if (!isset($field['name']))
            return;

        foreach (['placeholder', 'value', 'id', 'class'] as $key => $value) {
            if (isset($field[$value])) {
                $options[$value] = $field[$value];
            }
        }

        $input = $form->field($model, $field['name'])->input($field['type'], $options);
        $input->label($field['label'] ?? false);

        return $input;
    }

    /**
     * Renders a text area.
     * @param yii\bootstrap\ActiveForm $form
     * @param DynamicModel $model
     * @param array $field
     * @return $this The field object itself.
     */
    public static function textarea($form, $model, $field) {
        $options = [];
        $template = "{label}\n{input}\n{hint}\n{error}";

        if (!isset($field['name']))
            return;

        foreach (['placeholder', 'value', 'id', 'class'] as $key => $value) {
            if (isset($field[$value])) {
                $options[$value] = $field[$value];
            }
        }

        $text_area = $form->field($model, $field['name'])->textArea($options);
        $text_area->label($field['label'] ?? false);

        return $text_area;
    }

    /**
     * Renders a list of radio buttons.
     * @param yii\bootstrap\ActiveForm $form
     * @param DynamicModel $model
     * @param array $field
     * @return $this The field object itself.
     */
    public static function radio($form, $model, $field) {

        $items = [];
        $checked = [];

        foreach ($field['items'] as $key => $value) {
            $items[$value['value']] = $value['text'];
            if (isset($value['checked'])) {
                $checked[] = $key + 1;
            }
        }

        $model->{$field['name']} = $checked;
        $radio_list = $form->field($model, $field['name'])->radioList($items);

        $label = (isset($field['label'])) ? $field['label'] : '';

        $radio_list->label($label, ['class' => 'bold']);

        return $radio_list;
    }

    /**
     * Renders a list of checkboxes.
     * @param yii\bootstrap\ActiveForm $form
     * @param DynamicModel $model
     * @param array $field
     * @return $this The field object itself.
     */
    public static function checkbox($form, $model, $field) {
        $items = [];
        $checked = [];

        foreach ($field['items'] as $key => $value) {

            $items[$value['value']] = $value['text'];
            if (isset($value['checked'])) {
                $checked[] = $key + 1;
            }
        }
        $items = ArrayHelper::map($field['items'], 'value', 'text');
        $model->{$field['name']} = $checked;
        $checkbox_list = $form->field($model, $field['name'])->checkboxList($items);

        $label = (isset($field['label'])) ? $field['label'] : '';
        $checkbox_list->label($label);

        return $checkbox_list;
    }

    /**
     * Renders a drop-down list.
     * @param yii\bootstrap\ActiveForm $form
     * @param DynamicModel $model
     * @param array $field
     * @return $this The field object itself.
     */
    public static function select($form, $model, $field) {
        if (ArrayHelper::keyExists('name', $field)) {
            $items = [];
            $checked = [];

            foreach ($field['items'] as $key => $value) {
                $items[$value['value']] = $value['text'];

                if (isset($value['checked'])) {
                    $checked[] = $key + 1;
                }
            }

            $model->{$field['name']} = $checked;
            $select = $form->field($model, $field['name'])->dropDownList($items);
            $label = (isset($field['label'])) ? $field['label'] : '';
            $select->label($label);
            return $select;
        }
    }

    /**
     * Renders a description html.
     * @param array $v
     * @return string
     */
    public static function description($v) {
        return $v['textdescription'];
    }

    /**
     * Renders a submit buton tag.
     * @param array $data
     * @return string The generated submit button tag
     */
    public static function submit($data) {
        return Html::submitButton($data['label'], ['class' => 'btn ' . $data['backgroundcolor']]);
    }

    /**
     * Send email
     * @param string $emailSender
     * @param string $to
     * @param string $subject
     * @param string $response
     * @return void
     */
    public function sendEmail($emailSender = null, $to = null, $subject = null, $response = null) {

        if (!is_string($emailSender) && !is_string($to)) {
            return;
        }

        SendEmail::widget([
            'from' => $emailSender,
            'to' => $to,
            'subject' => $subject,
            'textBody' => $response,
        ]);
    }

}
