<?php

$shortcodes = array(
	'short_code' => array(
		'label'      => 'Descriptive Name',
		'attributes' => array(
			'attr_name' => array(
				'label'      => 'Attribute Label',
				'type'       => 'text|select|radio|checkbox',
				'options'    => array(
					'value1' => array(
						'label'      => 'Descriptive Name for Value',
						'dependency' => 'jQuery selector',
					),
				),
				'default'    => 'value1',
				'dependency' => 'jQuery selector',
			),
		),
	),
);

$shortcodes = array(
	'simple'                   => array(
		'label'      => 'Simple',
		'attributes' => array(),
	),
	'text'                     => array(
		'label'      => 'Text',
		'attributes' => array(
			'textme' => array(
				'label'   => 'Text Me',
				'default' => 'Type something',
			),
		),
	),
	'checkbox'                 => array(
		'label'      => 'Checkbox',
		'attributes' => array(
			'attr1' => array(
				'label'   => 'Attr 1',
				'type'    => 'checkbox',
				'options' => array(
					'0' => array(
						'label' => 'Zero',
					),
					'1' => array(
						'label' => 'One',
					),
					'2' => array(
						'label' => 'Two',
					),
				),
			),
			'attr2' => array(
				'label'   => 'Attr 2',
				'type'    => 'checkbox',
				'options' => array(
					'0' => array(
						'label' => 'Zero',
					),
				),
				'default' => '0',
			),
		),
	),
	'complex'                  => array(
		'label'      => 'Complex',
		'attributes' => array(
			'format' => array(
				'label'   => 'Date Format',
				'type'    => 'select-multiple',
				'options' => array(
					'm/d/Y' => array(
						'label' => 'month/day/year',
					),
					'Y-m-d' => array(
						'label' => 'year-month-day',
					),
				),
				'default' => 'm/d/Y',
			),
		),
	),
	'attr_dependency_sample1'  => array(
		'label'      => 'Attribute Dependency Sample',
		'attributes' => array(
			'attr1' => array(
				'label'   => 'Attr 1',
				'type'    => 'radio',
				'options' => array(
					'0' => array(
						'label' => 'Zero',
					),
					'1' => array(
						'label' => 'One',
					),
				),
			),
			'attr2' => array(
				'label'      => 'Attr 2',
				'type'       => 'radio',
				'options'    => array(
					'0' => array(
						'label' => 'Zero',
					),
					'1' => array(
						'label' => 'One',
					),
				),
				'default'    => '0',
				'dependency' => '[name="attr1"][value="1"]:checked',
			),
		),
	),
	'option_dependency_sample' => array(
		'label'      => 'Option Dependency Sample',
		'attributes' => array(
			'attr1' => array(
				'label'   => 'Attr 1',
				'type'    => 'radio',
				'options' => array(
					'0' => array(
						'label' => 'Zero',
					),
					'1' => array(
						'label' => 'One',
					),
					'2' => array(
						'label' => 'Two',
					),
				),
			),
			'attr2' => array(
				'label'   => 'Attr 2',
				'type'    => 'radio',
				'options' => array(
					'0' => array(
						'label'      => 'Zero',
						'dependency' => '[name="attr1"][value="1"]:checked',
					),
					'1' => array(
						'label'      => 'One',
						'dependency' => '[name="attr1"][value="0"]:checked,[name="attr1"][value="1"]:checked',
					),
				),
				'default' => '0',
			),
		),
	),
);

foreach ( $shortcodes as $shortcode => $options ) {
	$complex = (int) ! empty( $options['attributes'] );
	printf( '<button class="shortcode" value="%s" data-complex="%s">%s</button>', $shortcode, $complex, $options['label'] );
	if ( $complex && is_array( $options['attributes'] ) ) {
		echo '<form data-shortcode="' . $shortcode . '" id="' . $shortcode . '" class="attributes">';
		foreach ( $options['attributes'] as $attr_name => $attr_options ) {
      $dependency = empty( $attr_options['dependency'] ) ? '' : sprintf( 'data-dependency="%s"', htmlentities( $attr_options['dependency'] ) );
			printf( '<div %s class="attribute col-%d">', $dependency, wlm_arrval( $attr_options, 'columns' ) ?: '12' );
			echo '<label>' . wlm_arrval( $attr_options, 'label' ) . '</label>';
			switch ( $attr_options['type'] ) {
				case 'select-multiple':
					$multiple = ' multiple="multiple"';
				case 'select':
					echo '<select name="' . $attr_name . '"' . $multiple . '>';
					foreach ( $attr_options['options'] as $value => $value_options ) {
						$selected = ( isset( $attr_options['default'] ) && $value == $attr_options['default'] ) ? ' selected="selected"' : '';
						$dependency = empty( $value_options['dependency'] ) ? '' : sprintf( 'data-dependency="%s"', htmlentities( $value_options['dependency'] ) );
						printf( '<option %s value="%s"%s>%s</option>', $dependency, $value, $selected, $value_options['label'] );
					}
					echo '</select>';
					break;
				case 'radio':
				case 'checkbox':
					foreach ( $attr_options['options'] as $value => $value_options ) {
						$checked = ( isset( $attr_options['default'] ) && $value == $attr_options['default'] ) ? ' checked="checked"' : '';
            $dependency = empty( $value_options['dependency'] ) ? '' : sprintf( 'data-dependency="%s"', htmlentities( $value_options['dependency'] ) );
						printf( '<label %s><input type="%s" name="%s" value="%s"%s> %s</label>', $dependency, $attr_options['type'], $attr_name, $value, $checked, $value_options['label'] );
					}
					break;
				case 'text':
				default:
					printf( '<input type="text" name="%s" value="%s" placeholder="%s">', $attr_name, $attr_options['default'], $attr_options['placeholder'] );
			}
			echo '</div>';
		}
		echo '</form>';
	}
}
?>
<style>
.attributes{
  display: none;
}
</style>

