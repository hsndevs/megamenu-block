/**
 * BLOCK: megamenu
 *
 * Registering a basic block with Gutenberg.
 * Simple block, renders and saves the same content without any interactivity.
 */

//  Import CSS.
import './editor.scss';
import './style.scss';

const { __ } = wp.i18n; // Import __() from wp.i18n
const { registerBlockType } = wp.blocks; // Import registerBlockType() from wp.blocks

/**
 * Register: aa Gutenberg Block.
 *
 * Registers a new block provided a unique name and an object defining its
 * behavior. Once registered, the block is made editor as an option to any
 * editor interface where blocks are implemented.
 *
 * @link https://wordpress.org/gutenberg/handbook/block-api/
 * @param  {string}   name     Block name.
 * @param  {Object}   settings Block settings.
 * @return {?WPBlock}          The block, if it has been successfully
 *                             registered; otherwise `undefined`.
 */
registerBlockType('cgb/block-megamenu', {
	// Block name. Block names must be string that contains a namespace prefix. Example: my-plugin/my-custom-block.
	title: __('megamenu - CGB Block'), // Block title.
	icon: 'shield', // Block icon from Dashicons → https://developer.wordpress.org/resource/dashicons/.
	category: 'common', // Block category — Group blocks together based on common traits E.g. common, formatting, layout widgets, embed.
	keywords: [
		__('megamenu — CGB Block'),
		__('CGB Example'),
		__('create-guten-block'),
	],
	attributes: {
		nav_menu: {
			type: 'object',
		},
		selected_nav: {
			type: 'string'
		}
	},

	/**
	 * The edit function describes the structure of your block in the context of the editor.
	 * This represents what the editor will render when the block is used.
	 *
	 * The "edit" property must be a valid function.
	 *
	 * @link https://wordpress.org/gutenberg/handbook/block-api/block-edit-save/
	 *
	 * @param {Object} props Props.
	 * @returns {Mixed} JSX Component.
	 */
	edit: (props) => {
		// Creates a <p class='wp-block-cgb-block-megamenu'></p>.

		if (!props.attributes.nav_menu) {
			wp.apiFetch({
				url: '/wp-json/wp/v2/menu'
			}).then(nav_menu => {
				props.setAttributes({
					nav_menu: nav_menu
				})
			})
		}

		if (!props.attributes.nav_menu) {
			return 'MegaMenu Loading...'
		}

		if (props.attributes.nav_menu && props.attributes.nav_menu.length === 0) {
			return 'No Menu item found...'
		}

		console.log(props.attributes);

		const updateNavigation = (e) => {
			props.setAttributes({
				selected_nav: e.target.value,
			});
		}

		return (
			<div className={props.className}>
				<select onChange={updateNavigation} value={props.attributes.selected_nav}>
					{
						props.attributes.nav_menu.map(nav_item => {
							return (
								<option value={nav_item.term_id} key={nav_item.term_id}>{nav_item.name}</option>
							)
						})
					}
				</select>
			</div>
		);
	},

	/**
	 * The save function defines the way in which the different attributes should be combined
	 * into the final markup, which is then serialized by Gutenberg into post_content.
	 *
	 * The "save" property must be specified and must be a valid function.
	 *
	 * @link https://wordpress.org/gutenberg/handbook/block-api/block-edit-save/
	 *
	 * @param {Object} props Props.
	 * @returns {Mixed} JSX Frontend HTML.
	 */
	save: () => {
		return null;
	},
});
