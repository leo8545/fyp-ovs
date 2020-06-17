<?php

namespace OVS\Utils;

class Form {

	static public function open(string $url, string $method = "post", ?array $attributes = [] ) {
		$attr_html = Form::get_attributes_html($attributes);
		return "<form action=\"$url\" method=\"$method\" $attr_html>";
	}

	static public function close() {
		return "</form>";
	}

	static public function label(string $for, string $label) {
		return "<label for=\"$for\">$label</label>";
	}

	static public function input( string $type, string $name, string $value = "", ?array $attributes = [], bool $checked = false ) {
		$attr_html = Form::get_attributes_html($attributes);
		if( isset($attributes["id"]) && !empty($attributes["id"]) ) {
			$id = $attributes["id"];
		} else {
			$id = $name;
		}
		$result = "<input type=\"$type\" name=\"$name\" id=\"$id\"";
		if( !empty( $value ) ) {
			$result .= " value=\"$value\" ";
		}
		$result .= " $attr_html ";
		if( $type === "radio" || $type === "checkbox" ) {
			$result .= isset($checked) && $checked ? " checked=\"checked\"" : "";
		}
		$result .= " />";
		return $result;
	}

	static public function text(string $name, string $value = "", ?array $attributes = []) {
		return self::input("text", $name, $value, $attributes);
	}

	static public function password(string $name, string $value = "", ?array $attributes = []) {
		return self::input("password", $name, $value, $attributes);
	}

	static public function radio(string $name, string $value = "", ?array $attributes = [], bool $checked = false) {
		return self::input("radio", $name, $value, $attributes, $checked);
	}

	static public function checkbox( string $name, string $value = "", ?array $attributes = [] ) {
		$checked = checked("yes", $value);
		$value = empty($value) ? "yes" : $value;
		return self::input("checkbox", $name, $value, $attributes, $checked);
	}

	static public function file( string $name, string $value = "", ?array $attributes = [] ) {
		return self::input("file", $name, $value, $attributes);
	}

	static public function group(array $meta) {

		$res = "";
		
		if( isset($meta["label"]) && isset($meta["field"]) ) {
			
			$res = self::open_field_wrapper();

			$label_for = isset( $meta["label"]["for"] ) ? $meta["label"]["for"] : "";
			$label_text = isset( $meta["label"]["text"] ) ? $meta["label"]["text"] : "";
			$field_type = isset($meta["field"]["type"]) ? $meta["field"]["type"] : "";
			$field_name = isset($meta["field"]["name"]) ? $meta["field"]["name"] : "";
			$field_value = isset($meta["field"]["value"]) ? $meta["field"]["value"] : "";
			$field_checked = isset($meta["field"]["checked"]) ? $meta["field"]["checked"] : "";
			$attributes = isset($meta["field"]["attributes"]) ? $meta["field"]["attributes"] : [];

			switch($field_type) {
				case "text":
				case "password":
					$res .= self::label($label_for, $label_text);
					$res .= self::$field_type($field_name, $field_value, $attributes);
				break;
				case "radio":
					$res .= self::$field_type($field_name, $field_value, $attributes, $field_checked);
					$res .= self::label($label_for, $label_text);
				break;
				case "textarea":
					$res .= self::label($label_for, $label_text);
					$res .= self::textarea($field_name, $field_value, $attributes);
				break;
				case "checkbox":
					// $checked = checked("yes", $field_value);
					// $field_value = empty($field_value) ? "yes" : $field_value;
					$res .= self::label($label_for, $label_text);
					$res .= self::checkbox($field_name, $field_value, $attributes );
				break;
			}
			$res .= "</div>";
		}

		return $res;
	}

	static public function button(string $text = "Submit", ?array $attributes = []) {
		$attr_html = Form::get_attributes_html($attributes);
		return "<button $attr_html>$text</button>";
	}
	
	static public function select( string $name, array $options, $selected_value = "", ?array $attributes = [] ) {
		$attr_html = Form::get_attributes_html($attributes);
		$html = "<select name=\"$name\" id=\"$name\" $attr_html>";
		$selected = "";
		foreach( $options as $value => $label ) {
			if( $value === $selected_value ) {
				$selected = "selected";
			} else {
				$selected = "";
			}
			$html .= "<option value=\"$value\" $selected>$label</option>";
		}
		$html .= "</select>";
		return $html;
	}

	static public function textarea( string $name, string $value, ?array $attributes = []  ) {
		$attr_html = Form::get_attributes_html($attributes);
		return "<textarea name=$name $attr_html>$value</textarea>";
	}

	static public function open_field_wrapper() {
		return "<div class='form-field'>";
	}

	static public function close_field_wrapper() {
		return "</div>";
	}

	static public function get_attributes_html( array $attributes ) : string {
		$attr_html = "";
		foreach($attributes as $attribute_name => $attribute_value) {
			$attr_html .= "$attribute_name=\"$attribute_value\"";
		}
		return $attr_html;
	}
}