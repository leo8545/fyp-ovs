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

	static public function text(string $name, string $value = "", ?array $attributes = []) {
		$attr_html = Form::get_attributes_html($attributes);
		return "<input type=\"text\" name=$name id=$name value=$value $attr_html />";
	}

	static public function radio(string $name, string $value, bool $checked = false) {
		$check_html = isset($checked) && $checked ? " checked=\"checked\"" : "";
		return "<input type=\"radio\" name=$name id=$value value=$value $check_html />";
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
			$attributes = isset($meta["field"]["attributes"]) ? $meta["field"]["attributes"] : [];

			$res .= self::label($label_for, $label_text);
			switch($field_type) {
				case "text":
					$res .= self::text($field_name, $field_value, $attributes);
				break;
			}
			$res .= "</div>";
		}

		return $res;
	}

	static public function submit(string $text = "Submit", ?array $attributes = []) {
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