<?php
/**
 * PickleJar Live for Artists & Venues - Manager Callbacks
 *
 * @package PickleJar Live for Artists & Venues
 */

namespace Picklejar\Api\Callbacks;

use Picklejar\Base\BaseController;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Class ManagerCallbacks
 *
 * @extends BaseController
 * @since 1.0.0
 * @package Picklejar\Api\Callbacks
 */
class ManagerCallbacks extends BaseController {
	/**
	 * Function sanitize the input fields
	 *
	 * @param mixed $input - input fields.
	 *
	 * @return mixed
	 * @since 1.0.0
	 * @access public
	 */
	public function pj_settings_sanitize( $input ) {
		$output = $input;
		if ( empty( $input['settings']['pj_validation_token'] ) ) {
			$output['settings'] = $this->pj_input_sanitize( $input['settings'], true );
			$output['manager']  = $this->pj_checkbox_sanitize( $input['manager'], true );
		} else {
			if ( isset( $output['settings'] ) ) {
				$output['settings'] = $this->pj_input_sanitize( $input['settings'] );
				if ( ! empty( $output['settings']['picklejar_events_layout_manager_page_id'] ) ) {
					$new_event_page_id = $output['settings']['picklejar_events_layout_manager_page_id'];
					$this->pj_trigger_update_post( $new_event_page_id, self::EVENTS_DEFAULT_PAGE_TEMPLATE );
				}

				if ( ! empty( $output['settings']['picklejar_artist_layout_manager_page_id'] ) ) {
					$new_event_page_id = $output['settings']['picklejar_artist_layout_manager_page_id'];
					$this->pj_trigger_update_post( $new_event_page_id, self::ARTIST_DEFAULT_PAGE_TEMPLATE );
				}

				if ( ! empty( $output['settings']['picklejar_events_layout_manager_page_details_id'] ) ) {
					$new_event_page_id = $output['settings']['picklejar_events_layout_manager_page_details_id'];
					$this->pj_trigger_update_post( $new_event_page_id, self::EVENTS_DETAILS_DEFAULT_PAGE_TEMPLATE );
				}

				if ( ! empty( $output['settings']['picklejar_artist_layout_manager_page_details_id'] ) ) {
					$new_event_page_id = $output['settings']['picklejar_artist_layout_manager_page_details_id'];
					$this->pj_trigger_update_post( $new_event_page_id, self::ARTIST_DETAILS_DEFAULT_PAGE_TEMPLATE );
				}
			}

			if ( isset( $output['manager'] ) ) {
				$output['manager'] = $this->pj_checkbox_sanitize( $input['manager'], null );
			}
		}

		return $output;
	}

	/**
	 * Function pj_layout_settings_sanitize.
	 *
	 * @param array $input - input fields.
	 *
	 * @return array
	 * @since 1.0.0
	 * @access public
	 */
	public function pj_layout_settings_sanitize( $input ) {
		$output = array();
		foreach ( $input as $key => $value ) {
			if ( is_array( $value ) ) {
				$output[ $key ] = $this->pj_input_sanitize( $value );
			} else {
				$get_value = ltrim( $value );
				if ( ! empty( $get_value ) ) {
					$output[ $key ] = $value;
				}
			}
		}

		return $output;
	}

	/**
	 * Function pj_input_sanitize
	 *
	 * @param array $input - input fields.
	 * @param null  $reset - reset fields.
	 *
	 * @return array
	 * @since 1.0.0
	 * @access public
	 */
	public function pj_input_sanitize(
		$input,
		$reset = null
	) {
		$output = array();

		foreach ( $input as $key => $value ) {
			$get_value = ltrim( $value );

			if ( $reset ) {
				$output[ $key ] = null;
			} else {
				if ( ! empty( $get_value ) ) {
					$output[ $key ] = $value;
				}
			}
		}

		return $output;
	}

	/**
	 * Function pj_checkbox_sanitize
	 *
	 * @param array $input - input fields.
	 * @param null  $reset - reset fields.
	 *
	 * @return array
	 * @since 1.0.0
	 * @access public
	 */
	public function pj_checkbox_sanitize(
		$input,
		$reset = null
	) {
		$output = array();
		foreach ( $this->managers as $key => $value ) {
			if ( ! empty( $reset ) ) {
				$output[ $key ] = null;
			} else {
				$output[ $key ] = isset( $input[ $key ] ) ? '1' : null;
			}
		}

		return $output;
	}

	/**
	 * Function pj_admin_section_manager
	 *
	 * @return void
	 * @since 1.0.0
	 * @access public
	 */
	public function pj_admin_section_manager() {
		echo 'Manage the sections and features of this Plugin by activating the checkboxes from the following list.';
	}

	/**
	 * Function wp_editor
	 *
	 * @param array $args array of arguments.
	 *
	 * @return void
	 * @since 1.0.0
	 * @access public
	 */
	public function wp_editor( $args ) {
		$label_for         = $args['label_for'];
		$post_data_name    = $args['option_name'];
		$input_option_name = isset( $args['option_name_array'] ) ? $args['option_name'] . '[' . $args['option_name_array'] . ']' : $post_data_name;
		$name              = $input_option_name . '[' . $label_for . ']';
		$value             = $args['value'] ?? ( isset( get_option( $post_data_name )[ $name ] ) ? get_option( $post_data_name )[ $name ] : '' );
		$label             = $args['label'] ?? $this->capitalize( $label_for );
		$settings          = $args['attributes'];
		$wp_editor_args    = array(
			'wpautop'           => false,
			'media_buttons'     => $settings['media_buttons'] ?? false,
			'forced_root_block' => $settings['forced_root_block'] ?? false,
			'force_br_newlines' => $settings['force_br_newlines'] ?? true,
			'force_p_newlines'  => $settings['force_p_newlines'] ?? false,
			'default_editor'    => $settings['default_editor'] ?? 'TinyMCE',
			'drag_drop_upload'  => $settings['drag_drop_upload'] ?? false,
			'textarea_name'     => $settings['textarea_name'] ?? $name,
			'textarea_rows'     => $settings['textarea_rows'] ?? 10,
			'tabindex'          => $settings['tabindex'] ?? '-1',
			'editor_css'        => $settings['editor_css'] ?? '',
			'editor_class'      => $settings['editor_class'] ?? 'frontend-article-editor',
			'teeny'             => $settings['teeny'] ?? true,
			'dfw'               => $settings['dfw'] ?? false,
			'tinymce'           => $settings['tinymce'] ?? array(
				'toolbar1' => 'bold,italic,underline,undo,redo,align',
				'toolbar2' => '',
			),
			'quicktags'         => $settings['quicktags'] ?? false,
		);

		$name = 'thecodehubs_bar_id';

		echo '<div class="picklejar-form-group">';
		echo '<label for="' . esc_attr( $name ) . '">' . esc_html( $label ) . '</label>';
		wp_editor( stripcslashes( $value ), $settings['id'], $wp_editor_args );
		echo '</div>';
	}

	/**
	 * Function pj_select_field
	 *
	 * @param array $args array of arguments.
	 *
	 * @return void
	 * @since 1.0.0
	 * @access public
	 */
	public function pj_select_field( $args ) {
		$label_for         = $args['label_for'] ?? null;
		$post_data_name    = $args['option_name'] ?? null;
		$input_option_name = isset( $args['option_name_array'] ) ? $args['option_name'] . '[' . $args['option_name_array'] . ']' : $post_data_name;
		$name              = $input_option_name . '[' . $label_for . ']';
		$post_datas        = $args['options'];
		$label             = $args['label'] ?? $this->capitalize( $label_for );
		$id                = ! empty( $args['id'] ) ? $args['id'] : $name;
		$placeholder       = ! empty( $args['placeholder'] ) ? 'placeholder="' . $args['placeholder'] . '"' : '';
		$value             = $args['value'] ?? get_option( $post_data_name )[ $name ] ?? get_option( $post_data_name )[ $label_for ] ?? $default_value ?? '';
		if ( ! empty( $args['option_name_array'] ) ) :
			$value = ( isset( get_option( $post_data_name )[ $args['option_name_array'] ][ $label_for ] ) ? get_option( $post_data_name )[ $args['option_name_array'] ][ $label_for ] : $value );
		endif;
		$class               = $args['class'] ?? null;
		$form_group_class    = $args['form_group_class'] ?? '';
		$attributes          = $args['attributes'] ?? '';
		$remove_first_option = $args['removeFirstOption'] ?? false;
		?>
		<div class="picklejar-form-group <?php echo esc_attr( $form_group_class ); ?>">
			<?php if ( ! empty( $label ) ) : ?>
				<label for="<?php echo esc_attr( $id ); ?>"><?php echo esc_html( $label ); ?></label>
			<?php endif; ?>
			<select
				id="<?php echo esc_attr( $id ); ?>"
				name="<?php echo esc_attr( $name ); ?>"
				class="picklejar-form-select <?php echo esc_attr( $class ); ?>"
				value="<?php echo esc_attr( $value ); ?>"
				data-selected="<?php echo esc_attr( $value ); ?>"
				<?php echo wp_kses( $attributes, picklejar_sanitize_custom_attributes() ); ?>
			>
				<?php if ( ! $remove_first_option ) : ?>
					<option value=""></option>
				<?php endif; ?>
				<?php foreach ( $post_datas as $key => $post_data ) : ?>
					<option
						value="<?php echo esc_attr( $key ); ?>"
						<?php echo (string) $value === (string) $key ? 'selected' : ''; ?>
					>
						<?php echo esc_attr( $post_data ); ?>
					</option>
				<?php endforeach; ?>
			</select>
		</div>
		<?php
	}

	/**
	 * Function isset_field.
	 *
	 * @param mixed $field field.
	 * @param mixed $default default.
	 *
	 * @return mixed|string
	 * @since 1.0.0
	 * @access public
	 */
	public function isset_field(
		$field,
		$default = ''
	) {
		return ( ! ! $field || isset( $field ) ) ? $field : $default;
	}

	/**
	 * Function toggle_field
	 *
	 * @param array $args array of arguments.
	 *
	 * @return void
	 * @since 1.0.0
	 * @access public
	 */
	public function toggle_field( $args ) {
		$label             = $args['label'] ?? null;
		$label_off         = $args['label_off'] ?? 'Off';
		$label_on          = $args['label_on'] ?? 'On';
		$label_for         = $args['label_for'];
		$post_data_name    = $args['option_name'];
		$input_option_name = isset( $args['option_name_array'] ) ? $args['option_name'] . '[' . $args['option_name_array'] . ']' : $post_data_name;
		$name              = $input_option_name . '[' . $label_for . ']';
		$id                = $args['label_for'] ?? $name;
		$class             = isset( $args['class'] ) ? ' ' . $args['class'] : '';
		$default_value     = $args['default_value'] ?? null;
		$value             = $args['value'] ?? get_option( $post_data_name )[ $name ] ?? get_option( $post_data_name )[ $label_for ] ?? $default_value ?? '';
		if ( ! empty( $args['option_name_array'] ) ) :
			$value = ( isset( get_option( $post_data_name )[ $args['option_name_array'] ][ $label_for ] ) ? get_option( $post_data_name )[ $args['option_name_array'] ][ $label_for ] : $value );
		endif;
		$checked    = $args['checked'] ?? false;
		$attributes = $args['attributes'] ?? '';
		?>
		<div class="picklejar-form-group">
			<?php echo $label ? '<p class="label-text">' . wp_kses_post( $label ) . '</p>' : ''; ?>
			<?php if ( ! empty( $label_off ) ) : ?>
				<span class="option-text"><?php echo esc_attr( $label_off ); ?></span>
			<?php endif; ?>
			<div class="picklejar-ui-toggle picklejar-ui-toggle-control">
				<input
					type="checkbox"
					id="<?php echo esc_attr( $id ); ?>"
					name="<?php echo esc_attr( $name ); ?>"
					value="<?php echo esc_attr( $value ); ?>"
					class="<?php echo esc_attr( $class ); ?>"
					<?php echo( $checked ? 'checked' : '' ); ?>
					<?php echo isset( $args['disabled'] ) && ! ! $args['disabled'] ? 'disabled' : false; ?>
					<?php echo wp_kses( $attributes, picklejar_sanitize_custom_attributes() ); ?>
				>
				<label
					for="<?php echo esc_attr( $id ); ?>"
					class="ui-toggle-label"
				></label>
			</div>
			<?php if ( ! empty( $label_on ) ) : ?>
				<span class="option-text"><?php echo esc_attr( $label_on ); ?></span>
			<?php endif; ?>
		</div>
		<?php
	}

	/**
	 * Function manager_checkbox_field
	 *
	 * @param array $args array of arguments.
	 *
	 * @return void
	 * @since 1.0.0
	 * @access public
	 */
	public function manager_checkbox_field( $args ) {
		$name        = $args['label_for'];
		$class       = $args['class'];
		$option_name = $args['option_name'];
		$checkbox    = get_option( $option_name );
		$checked     = isset( $checkbox[ $name ] ) && $checkbox[ $name ];
		?>
		<div class="picklejar-ui-toggle-control">
			<input
				type="checkbox"
				id="<?php echo esc_attr( $name ); ?>"
				name="<?php echo esc_attr( $option_name ) . '[' . esc_attr( $name ) . ']'; ?>"
				value="1"
				class="<?php echo esc_attr( $class ); ?>"
				<?php echo $checked ? 'checked' : ''; ?>
			>
			<label
				for="<?php echo esc_attr( $name ); ?>"
				class="ui-toggle-label"
			>
				<div></div>
			</label>
		</div>
		<?php
	}

	/**
	 * Function checkbox_field
	 *
	 * @param array $args array of arguments.
	 *
	 * @return void
	 * @since 1.0.0
	 * @access public
	 */
	public function checkbox_field( $args ) {
		$label_for         = $args['label_for'];
		$post_data_name    = $args['option_name'];
		$input_option_name = isset( $args['option_name_array'] ) ? $args['option_name'] . '[' . $args['option_name_array'] . ']' : $post_data_name;
		$name              = $input_option_name . '[' . $label_for . ']';
		$label             = $args['label'] ?? '';
		$class             = $args['class'] ?? null;
		$checkbox          = get_option( $post_data_name );
		$value             = $args['value'] ?? 1;
		$checked           = $args['checked'] ?? ( isset( $checkbox[ $name ] ) && $checkbox[ $name ] );
		?>
		<div class="<?php echo esc_attr( $class ); ?>">
			<label for="<?php echo esc_attr( $name ); ?>">
				<?php echo esc_html( $label ); ?>
			</label>
			<input
				type="checkbox"
				id="<?php echo esc_attr( $name ); ?>"
				name="<?php echo esc_attr( $name ); ?>"
				value="<?php echo esc_attr( $value ); ?>"
				class="<?php echo esc_attr( $class ); ?>" <?php echo esc_attr( $checked ? 'checked' : '' ); ?>
			>
		</div>
		<?php
	}

	/**
	 * Function radio_button_field
	 *
	 * @param array $args array of arguments.
	 *
	 * @return void
	 * @since 1.0.0
	 * @access public
	 */
	public function radio_button_field( $args ) {
		$name        = $args['option_name'];
		$id          = isset( $args['id'] ) ? $args['id'] : $name;
		$label       = isset( $args['label'] ) ? ( isset( $args['icon_only'] ) ? '' : $args['label'] ) : '';
		$class       = isset( $args['class'] ) ? ' ' . $args['class'] : '';
		$value       = isset( $args['value'] ) ? $args['value'] : 1;
		$checked     = isset( $args['selected'] ) ? true : false;
		$icon_class  = ' class="ml-social-icon ';
		$icon_class .= isset( $args['iconClass'] ) ? $args['iconClass'] : '';
		$icon_class .= '"';
		$icon_format = isset( $args['icon_format'] ) ? $args['icon_format'] : '';
		$icon        = isset( $args['icon'] ) ? ( 'svg' === $icon_format ? $args['icon'] : '<img' . $icon_class . ' src="' . $args['icon'] . '">' ) : false;
		?>
		<div class="picklejar-form-group  <?php echo esc_attr( $class ); ?>">
			<input
				type="radio"
				id="<?php echo esc_attr( $id ); ?>"
				name="<?php echo esc_attr( $name ); ?>"
				value="<?php echo esc_attr( $value ); ?>"
				class="<?php echo esc_attr( $class ); ?>"
				<?php echo $checked ? 'checked' : ''; ?>
			>
			<label for="<?php echo esc_attr( $id ); ?>"><?php echo esc_attr( $label ); ?><?php echo esc_attr( $icon ); ?></label>
		</div>
		<?php
	}

	/**
	 * Function button_field
	 *
	 * @param array $args array of arguments.
	 *
	 * @return void
	 * @since 1.0.0
	 * @access public
	 */
	public function button_field( $args ) {
		$label_for         = $args['label_for'];
		$post_data_name    = $args['option_name'];
		$input_option_name = isset( $args['option_name_array'] ) ? $args['option_name'] . '[' . $args['option_name_array'] . ']' : $post_data_name;
		$name              = $input_option_name . '[' . $label_for . ']';
		$label             = $args['label'] ?? $this->capitalize( $label_for );
		$id                = ! empty( $args['id'] ) ? $args['id'] : $name;
		$class             = ! empty( $args['class'] ) ? ' ' . $args['class'] : '';
		$value             = $args['value'] ?? get_option( $post_data_name )[ $name ] ?? get_option( $post_data_name )[ $label_for ] ?? $default_value ?? '';
		if ( ! empty( $args['option_name_array'] ) ) :
			$value = ( isset( get_option( $post_data_name )[ $args['option_name_array'] ][ $label_for ] ) ? get_option( $post_data_name )[ $args['option_name_array'] ][ $label_for ] : $value );
		endif;
		$disabled   = isset( $args['disable'] ) ? $args['disabled'] : false;
		$required   = isset( $args['required'] ) ? 'required' : '';
		$attributes = $args['attributes'] ?? 'required';
		$type       = $args['type'] ?? 'button';
		?>

		<div class="picklejar-form-group">
			<div class="input-group mb-3">
				<button
					type="<?php echo esc_attr( $type ); ?>"
					class="button <?php echo esc_attr( $class ); ?>"
					id="<?php echo esc_attr( $id ); ?>"
					<?php echo wp_kses( $attributes, picklejar_sanitize_custom_attributes() ); ?>
					<?php echo esc_attr( $required ); ?>
					<?php echo( $disabled ? 'readonly' : '' ); ?>
				>
					<?php echo esc_html( $label ); ?>
				</button>
			</div>
		</div>
		<?php
	}

	/**
	 * Function text_field
	 *
	 * @param array $args array of arguments.
	 *
	 * @return void
	 * @since 1.0.0
	 * @access public
	 */
	public function text_field( $args ) {
		$label_for         = $args['label_for'];
		$post_data_name    = $args['option_name'];
		$input_option_name = isset( $args['option_name_array'] ) ? $args['option_name'] . '[' . $args['option_name_array'] . ']' : $post_data_name;
		$name              = $input_option_name . '[' . $label_for . ']';
		$label             = $args['label'] ?? $this->capitalize( $label_for );
		$hide_label        = $args['hide_label'] ?? false;
		$id                = ! empty( $args['id'] ) ? $args['id'] : $name;
		$type              = ! empty( $args['type'] ) ? $args['type'] : 'text';
		if ( 'alignment' === $type ) {
			$type = 'hidden';
		}
		$placeholder   = ! empty( $args['placeholder'] ) ? 'placeholder="' . $args['placeholder'] . '"' : ( 'date' === $type ? 'placeholder="yyyy-mm-dd"' : '' );
		$class_label   = ! empty( $args['class_label'] ) ? ' class="' . $args['class_label'] . '"' : '';
		$class         = ! empty( $args['class'] ) ? ' ' . $args['class'] : '';
		$default_value = $args['default_value'] ?? '';
		$value         = $args['value'] ?? get_option( $post_data_name )[ $name ] ?? get_option( $post_data_name )[ $label_for ] ?? $default_value ?? '';
		if ( ! empty( $args['option_name_array'] ) ) :
			$value = ( isset( get_option( $post_data_name )[ $args['option_name_array'] ][ $label_for ] ) ? get_option( $post_data_name )[ $args['option_name_array'] ][ $label_for ] : $value );
		endif;
		$disabled   = isset( $args['disable'] ) ? $args['disabled'] : false;
		$required   = isset( $args['required'] ) ? 'required' : '';
		$attributes = $args['attributes'] ?? '';
		?>
		<div class="picklejar-form-group">
			<?php if ( ! empty( $label ) && ! $hide_label ) : ?>
				<?php echo '<label for="' . esc_attr( $id ) . '" class="' . esc_attr( $class_label ) . '">' . esc_html( $label ) . '</label>'; ?>
			<?php endif; ?>
			<?php if ( 'number' === $type ) : ?>
			<div class="picklejar-input-group mb-3">
				<button
					class="picklejar-btn picklejar-btn-outline-secondary picklejar-remove"
					type="button"
				>-
				</button>
				<?php endif; ?>

				<input
					type="<?php echo esc_attr( $type ); ?>"
					class="picklejar-form-control widefat <?php echo esc_attr( $class ); ?>"
					id="<?php echo esc_attr( $id ); ?>"
					name="<?php echo esc_attr( $name ); ?>"
					value="<?php echo esc_attr( $value ); ?>"
					<?php echo esc_attr( $placeholder ); ?>
					<?php echo esc_attr( $required ); ?>
					<?php echo( $disabled ? 'readonly' : '' ); ?>
					<?php echo 'color' === $type ? 'data-default="' . esc_attr( $default_value ) . '"' : ''; ?>
					<?php echo wp_kses( $attributes, picklejar_sanitize_custom_attributes() ); ?>
				>

				<?php if ( 'date' === $type ) : ?>
					<span class="material-symbols-outlined">event</span>
				<?php endif; ?>

				<?php if ( 'color' === $type ) : ?>
					<!--span class="pj-clear-color">Clear</span-->
				<?php endif; ?>

				<?php if ( 'number' === $type ) : ?>
				<button
					class="picklejar-btn picklejar-btn-outline-secondary picklejar-add"
					type="button"
				>+
				</button>
			</div>
		<?php endif; ?>


			<?php if ( 'alignment' === $args['type'] ) : ?>
				<?php $alignment_values = array( 'left', 'center', 'right', 'justify' ); ?>
				<div class="picklejar-input-group mb-3">
					<?php foreach ( $alignment_values as $alignment_value ) : ?>
						<button
							type="button"
							class="picklejar-text-alignment button <?php echo $value === $alignment_value ? 'button-primary' : ''; ?>"
							data-alignment="<?php echo esc_attr( $alignment_value ); ?>"
						><span class="material-symbols-outlined">format_align_<?php echo esc_html( $alignment_value ); ?></span></button>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>
		</div>
		<?php
	}

	/**
	 * Text area field.
	 *
	 * @param array $args array of arguments.
	 *
	 * @return void
	 * @since 1.0.0
	 * @access public
	 */
	public function text_area( $args ) {
		$label_for         = $args['label_for'];
		$post_data_name    = $args['option_name'];
		$input_option_name = isset( $args['option_name_array'] ) ? $args['option_name'] . '[' . $args['option_name_array'] . ']' : $post_data_name;
		$name              = $input_option_name . '[' . $label_for . ']';
		$label             = isset( $args['label'] ) ? $args['label'] : $this->capitalize( $label_for );
		$id                = isset( $id ) ? $id : $name;
		$placeholder       = isset( $args['placeholder'] ) ? 'placeholder="' . $args['placeholder'] . '"' : '';
		$class_label       = isset( $args['class_label'] ) ? ' class="' . $args['class_label'] . '"' : '';
		$class             = isset( $args['class'] ) ? ' ' . $args['class'] : '';
		$value             = isset( $args['value'] ) ? $args['value'] : ( isset( get_option( $post_data_name )[ $name ] ) ? get_option( $post_data_name )[ $name ] : '' );
		if ( isset( $args['option_name_array'] ) ) :
			$value = ( isset( get_option( $post_data_name )[ $args['option_name_array'] ][ $label_for ] ) ? get_option( $post_data_name )[ $args['option_name_array'] ][ $label_for ] : $value );
		endif;

		$disabled   = isset( $args['disable'] ) ? $args['disabled'] : false;
		$required   = isset( $args['required'] ) ? 'required' : '';
		$attributes = $args['attributes'] ?? '';
		?>
		<div class="picklejar-form-group">
			<?php if ( isset( $label ) ) : ?>
				<label
					for="<?php esc_attr( $id ); ?>"
					class="<?php esc_attr( $class_label ); ?>"
				>
					<?php echo esc_html( $label ); ?>
				</label>
			<?php endif; ?>
			<textarea
				class="picklejar-form-control widefat <?php echo esc_attr( $class ); ?>"
				rows="5"
				id="<?php echo esc_attr( $id ); ?>"
				name="<?php echo esc_attr( $name ); ?>"
				<?php echo esc_attr( $placeholder ); ?>
				<?php echo esc_attr( $required ); ?>
				<?php echo( $disabled ? 'readonly' : '' ); ?>
				<?php echo wp_kses( $attributes, picklejar_sanitize_custom_attributes() ); ?>
			>
				<?php echo esc_html( $value ); ?>
			</textarea>
		</div>
		<?php
	}

	/**
	 * Image field.
	 *
	 * @param array $args array of arguments.
	 *
	 * @return void
	 * @since 1.0.0
	 * @access public
	 */
	public function img_field( $args ) {
		$label_for         = $args['label_for'];
		$post_data_name    = $args['option_name'];
		$input_option_name = isset( $args['option_name_array'] ) ? $args['option_name'] . '[' . $args['option_name_array'] . ']' : $post_data_name;
		$name              = $input_option_name . '[' . $label_for . ']';
		$label             = isset( $args['label'] ) ? $args['label'] : null;
		$id                = isset( $id ) ? $id : $name;
		$type              = isset( $args['type'] ) ? $args['type'] : 'text';
		$placeholder       = isset( $args['placeholder'] ) ? 'placeholder="' . $args['placeholder'] . '"' : '';
		$class_label       = isset( $args['class_label'] ) ? ' class="' . $args['class_label'] . '"' : '';
		$class             = isset( $args['class'] ) ? ' ' . $args['class'] : '';
		$multiple          = isset( $args['multiple'] ) ? $args['multiple'] : '';
		$img_width         = isset( $args['img_width'] ) ? $args['img_width'] : '100%';
		$img_height        = isset( $args['img_height'] ) ? $args['img_height'] : 'auto';

		$value = isset( $args['value'] ) ? $args['value'] : ( isset( get_option( $post_data_name )[ $name ] ) ? get_option( $post_data_name )[ $name ] : '' );
		if ( isset( $args['option_name_array'] ) ) :
			$value = ( isset( get_option( $post_data_name )[ $args['option_name_array'] ][ $label_for ] ) ? get_option( $post_data_name )[ $args['option_name_array'] ][ $label_for ] : $value );
		endif;

		$disabled   = isset( $args['disable'] ) ? $args['disabled'] : false;
		$required   = isset( $args['required'] );
		$attributes = $args['attributes'] ?? '';
		?>
		<div class="picklejar-form-group img-upload-container js-img-upload-container">
			<ul
				class="gallery-wrapper"
				data-width="<?php echo esc_attr( $img_width ); ?>"
				data-height="<?php echo esc_attr( $img_height ); ?>"
			>
				<li class="picklejar-form-control img-item js-img-item">
					<?php
					if ( ! empty( $value ) ) :
						$img_list = explode( ',', $value );
						foreach ( $img_list as $key => $img ) :
							?>
							<div
								class="default-img img-thumbnail picklejar-img-wrapper picklejar-d-flex justify-content-center align-items-center"
								style="width:<?php echo esc_attr( $img_width ); ?>; height: <?php echo esc_attr( $img_height ); ?>"
							>
								<button
									type="button"
									class="picklejar-btn picklejar-btn-remove remove js-img-remove"
									id="<?php echo esc_attr( $img ); ?>"
								>&times;
								</button>
								<img
									src="<?php echo esc_url( wp_get_attachment_image_src( $img, 'thumbnail' )[0] ); ?>"
									alt=""
								/>
							</div>
							<?php
						endforeach;
					else :
						?>
						<div
							class="default-img picklejar-d-flex justify-content-center align-items-center"
							style="width:<?php echo esc_attr( $img_width ); ?>; height: <?php echo esc_attr( $img_height ); ?>"
						>
							<h3><?php echo esc_attr( $img_width ); ?> x <?php echo esc_attr( $img_height ); ?></h3>
						</div>
						<?php
					endif;
					?>
				</li>
			</ul>
			<label
				for="<?php echo esc_attr( $id ); ?>"
				<?php if ( isset( $class_label ) ) : ?>
					class="<?php echo esc_attr( $class_label ); ?>"
				<?php endif; ?>
			>
				<?php echo esc_html( $label ); ?>
			</label>
			<input
				type="hidden"
				class="image-upload <?php echo esc_attr( $class ); ?>"
				id="<?php echo esc_attr( $name ); ?>"
				name="<?php echo esc_attr( $name ); ?>"
				value="<?php echo esc_attr( $value ); ?>"
				<?php echo wp_kses( $attributes, picklejar_sanitize_custom_attributes() ); ?>
			>
			<button
				type="button"
				class="button button-primary js-image-upload"
				<?php
				if ( $multiple ) {
					echo 'multiple="multiple"';
				}
				?>
			> Select
				<?php
				if ( $multiple ) {
					echo 'Gallery Images';
				} else {
					echo 'Image';
				}
				?>
			</button>
		</div>
		<?php
	}

	/**
	 * Capitalize Function
	 *
	 * @param string $string array of arguments.
	 *
	 * @return string
	 * @since 1.0.0
	 * @access public
	 */
	public function capitalize( $string ) {
		$text = ucwords( $string, '_' );

		return str_replace( '_', ' ', $text );
	}

	/**
	 * Function PJ Events Layout Input
	 *
	 * @param array $args array of arguments.
	 *
	 * @return void
	 * @since 1.0.0
	 * @access public
	 */
	public function pj_events_layout_input( $args ) {
		$label_for         = $args['label_for'];
		$option_name       = $args['option_name'];
		$input_option_name = isset( $args['option_name_array'] ) ? $args['option_name'] . '[' . $args['option_name_array'] . ']' : $option_name;
		$name              = $input_option_name . '[' . $label_for . ']';
		$id                = $args['id'] ?? $name;
		$class             = isset( $args['class'] ) ? ' ' . $args['class'] : '';
		$layout_type       = array( 'grid', 'slider' );
		$value             = isset( $args['value'] ) ? $args['value'] : ( isset( get_option( $option_name )[ $name ] ) ? get_option( $option_name )[ $name ] : '' );
		if ( isset( $args['option_name_array'] ) ) :
			$value = ( isset( get_option( $option_name )[ $args['option_name_array'] ][ $label_for ] ) ? get_option( $option_name )[ $args['option_name_array'] ][ $label_for ] : $value );
		endif;
		?>
		<?php foreach ( $layout_type as $layout ) : ?>
			<?php $checked = $layout === $value || 'grid' === $layout ? 'checked' : ''; ?>
			<div class="picklejar-form-group picklejar-card border">
				<label for="<?php echo esc_attr( $id . '-' . $layout ); ?>">
					<span class="label">
					<input
						type="radio"
						id="<?php echo esc_attr( $id . '-' . $layout ); ?>"
						name="<?php echo esc_attr( $name ); ?>"
						value="<?php echo esc_attr( $layout ); ?>"
						class="<?php echo esc_attr( $class ); ?>"
						<?php echo esc_attr( $checked ); ?>
					>
					<span class="label">
					<?php echo esc_html( $layout ); ?>
					</span>
				</span>
				</label>
				<?php
				switch ( $layout ) :
					case 'grid':
						$this->pj_render_event_grid_layout_field();
						break;
					case 'slider':
						$this->render_event_slider_layout_field();
						break;
				endswitch;
				?>
			</div>
			<?php
		endforeach;
	}

	/**
	 * Function Render General Input Field
	 *
	 * @param mixed        $model array of arguments.
	 * @param string       $page string.
	 * @param array        $data array of data.
	 * @param integer|null $post_id The post ID integer.
	 * @param string       $parent parent element.
	 *
	 * @return void
	 * @since 1.0.0
	 * @access public
	 */
	public function render_general_input_field(
		$model,
		$page,
		$data,
		$post_id = null,
		$parent = ''
	) {

		$post_data_name = ! empty( $parent ) ? $page . '[' . $parent . ']' : $page;
		foreach ( $model as $field => $field_data ) :
			if ( ! empty( $parent ) && isset( $data[ $parent ][ $field ] ) ) {
				$data[ $field ] = $data[ $parent ][ $field ];
			}
			$type              = $field_data['type'];
			$label             = $field_data['label'] ?? null;
			$option_array_name = $field_data['option_name_array'] ?? null;
			$default_value     = $field_data['default_value'] ?? null;
			$value             = $data[ $option_array_name ][ $field ] ?? $data[ $field ] ?? $default_value;
			$required          = $field_data['required'] ?? null;
			$class             = $field_data['class'] ?? null;
			$wrapper_before    = $field_data['wrapper_before'] ?? null;
			$wrapper_after     = $field_data['wrapper_after'] ?? null;
			$attributes        = $field_data['attributes'] ?? '';

			if ( ! empty( $wrapper_before ) ) {
				echo wp_kses_post( $wrapper_before );
			}

			$args = array(
				'class'              => $class,
				'label'              => $label,
				'label_for'          => $field,
				'option_name'        => $post_data_name,
				'option_name_array'  => $option_array_name,
				'type'               => $type,
				'show_in_quick_edit' => true,
				'value'              => $value ?? '',
				'required'           => $required,
				'attributes'         => $attributes,
			);

			switch ( $type ) :
				case 'wp_editor':
					$this->wp_editor( $args );
					break;

				case 'button':
				case 'submit':
					$this->button_field( $args );
					break;

				case 'text':
				case 'number':
				case 'color':
				case 'email':
				case 'date':
				case 'alignment':
					$this->text_field( $args );
					break;

				case 'textarea':
					$args['value'] = $value;
					$this->text_area( $args );
					break;

				case 'image':
					$args['value']      = $value;
					$args['img_width']  = $field_data['width'] ?? null;
					$args['img_height'] = $field_data['height'] ?? null;
					$this->img_field( $args );
					break;

				case 'checkbox':
					$args['checked'] = isset( $data[ $field ] ) ? true : null;
					$this->checkbox_field( $args );
					break;

				case 'toggle':
					$args['checked']     = ! ! ( isset( $data[ $field ] ) || ! empty( $args['value'] ) );
					$args['value']       = ! empty( $args['value'] ) ? $args['value'] : $post_id;
					$args['meta_box_cb'] = true;
					$args['label_off']   = $field_data['label_off'] ?? '';
					$args['label_on']    = $field_data['label_on'] ?? '';
					$this->toggle_field( $args );
					break;

				case 'select':
					$args['option_name']       = isset( $field_data['array'] ) ? $post_data_name . '[' . $post_id . ']' : $post_data_name;
					$args['value']             = isset( $field_data['array'] ) ? $data[ $post_id ][ $field ] : $value;
					$args['option_name_array'] = $option_array_name;
					$args['options']           = $field_data['options'];
					$args['meta_box_cb']       = true;
					$args['wrapper_before']    = $field_data['wrapper_before'] ?? null;
					$args['wrapper_after']     = $field_data['wrapper_after'] ?? null;
					$args['select_classes']    = $field_data['select_classes'] ?? null;
					$args['form_group_class']  = $field_data['form_group_class'] ?? null;
					if ( ! empty( $args['wrapper_before'] ) ) {
						echo wp_kses_post( $args['wrapper_before'] );
					}
					$this->pj_select_field( $args );
					if ( ! empty( $args['wrapper_after'] ) ) {
						echo wp_kses_post( $args['wrapper_after'] );
					}
					break;

				case 'array':
					echo '<div class="picklejar-form-group grouping">';
					echo '<h3>' . esc_html( $this->capitalize( $field ) ) . '</h3>';
					$this->render_general_input_field( $field_data['field_list'], $page, $data, $post_id, $field );
					echo '</div>';
					break;
				case 'pj_events_layout_input':
					$this->pj_events_layout_input( $args );
					break;
			endswitch;
			if ( ! empty( $wrapper_after ) ) {
				echo wp_kses_post( $wrapper_after );
			}
		endforeach;
	}

	/**
	 * Render a img_upload field.
	 *
	 * @param string  $field The field to render.
	 * @param string  $label The label for the field.
	 * @param string  $name The name of the field.
	 * @param boolean $multiple Whether or not the field is multiple.
	 *
	 * @return void
	 * @since 1.0.0
	 * @access public
	 */
	public function img_upload(
		$field,
		$label,
		$name,
		$multiple = false
	) {
		?>
		<div class="picklejar-form-group">
			<label for="<?php echo esc_attr( $name ); ?>">
				<?php esc_html( $label ); ?>
			</label>
			<ul class="gallery-wrapper">
				<?php
				$img_list = explode( ',', $field );
				foreach ( $img_list as $key => $img ) {
					?>
					<li>
						<div class="img-thumbnail img-wrapper">
							<button
								type="button"
								class="picklejar-btn delete js-img-remove"
							>X
							</button>
							<img
								src="<?php echo esc_url( wp_get_attachment_image_src( $img, 'thumbnail' )[0] ); ?>"
								alt=""
							/>
						</div>
					</li>
					<?php
				}
				?>
			</ul>
			<input
				type="hidden"
				class="image-upload"
				id="<?php echo esc_html( $name ); ?>"
				name="<?php echo esc_html( $name ); ?>"
				value="<?php echo esc_html( $field ); ?>"
			>
			<button
				type="button"
				class="button button-primary js-image-upload"
				<?php
				if ( $multiple ) {
					echo 'multiple="multiple"';
				}
				?>
			> Select
				<?php
				if ( $multiple ) {
					echo 'Gallery Images';
				} else {
					echo 'Image';
				}
				?>
			</button>
		</div>
		<?php
	}
}
