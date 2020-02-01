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
		$result = "<input type=\"$type\" name=\"$name\" id=\"$name\" value=\"$value\" $attr_html ";
		if( $type === "radio" ) {
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

	static public function group(array $meta) {

		$res = "";
		
		if( isset($meta["label"]) && isset($meta["field"]) ) {
			
			$res = "<div class=\"form-field\">";

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
					$res .= self::radio($field_name, $field_value, $attributes, $field_checked);
					$res .= self::label($label_for, $label_text);
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
	
	static public function get_attributes_html( array $attributes ) : string {
		$attr_html = "";
		foreach($attributes as $attribute_name => $attribute_value) {
			$attr_html .= "$attribute_name=\"$attribute_value\"";
		}
		return $attr_html;
	}
}