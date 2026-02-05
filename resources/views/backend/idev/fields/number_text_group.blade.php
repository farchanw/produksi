@php 
$prefix_repeatable = (isset($repeatable))? true : false;
$preffix_method = (isset($method))? $method."_": "";
@endphp
<div class="{{(isset($field['class']))?$field['class']:'form-group'}} field-number-text-group-{{(isset($field['name']))?$field['name']:'name_'.$key}}">
    <label>{{(isset($field['label']))?$field['label']:'Label '.$key}} 
        @if(isset($field['required']) && $field['required'])
        <small class="text-danger">*</small>
        @endif
    </label>

    <div class="input-group">
        @if (isset($field['prefix']))
        <span class="input-group-text">{{$field['prefix']}}</span>
        @endif

        <input 
            type="number" 
            id="{{$preffix_method}}{{(isset($field['name']))?str_replace("[]","",$field['name']):'id_'.$key}}" 
            name="{{(isset($field['name']))?$field['name']:'name_'.$key}}" 
            value="{{(isset($field['value']))?$field['value']:''}}" 
            class="form-control idev-form @if($prefix_repeatable) field-repeatable @endif">
        
        @if (isset($field['suffix']))
        <span class="input-group-text">{{$field['suffix']}}</span>
        @endif
    </div>
</div>