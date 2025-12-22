@php
$prefix_repeatable = (isset($repeatable))? true : false;
$select_id = (isset($field['name']))?$field['name']:'id_'.$key;
$select_name = (isset($field['name']))?$field['name']:'name_'.$key;
$preffix_method = (isset($method))? $method."_": "";
@endphp
<div class="{{(isset($field['class']))?$field['class']:'form-group'}}">
    <label>{{(isset($field['label']))?$field['label']:'Label '.$key}}
        @if(isset($field['required']) && $field['required'])
        <small class="text-danger">*</small>
        @endif
    </label>
    <select 
        id="{{$preffix_method}}{{$select_id}}" 
        name="{{$select_name}}[]" 
        class="form-control idev-form @if($prefix_repeatable) field-repeatable @endif" multiple>
        @foreach($field['options'] as $key => $opt)
        <option value="{{$opt['value']}}" 
        @if($opt['value'] == $field['value'] || $opt['value'] == request($select_name)) selected @endif
        >{{$opt['text']}}</option>
        @endforeach
    </select>
</div>