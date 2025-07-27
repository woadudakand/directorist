import { registerBlockType } from '@wordpress/blocks';
import { useBlockProps, InspectorControls } from '@wordpress/block-editor';
import ServerSideRender from '@wordpress/server-side-render';
import { Fragment, useState } from '@wordpress/element';
import { __ } from '@wordpress/i18n';
import { TypesControl } from '../controls';

import {
	PanelBody,
	PanelRow,
	SelectControl,
	ToggleControl,
	TextControl,
	__experimentalToggleGroupControl as ToggleGroupControl,
	__experimentalToggleGroupControlOption as ToggleGroupControlOption,
} from '@wordpress/components';

import {
	getAttsForTransform,
	isMultiDirectoryEnabled,
	getPlaceholder,
} from '../functions';
import metadata from './block.json';
import getLogo from '../logo';

const Placeholder = () => getPlaceholder('search');

const Divider = () => (
	<PanelRow>
		<hr
			style={{
				width: '100%',
				border: 0,
				borderTop: '1px solid #ddd',
				margin: '16px 0',
			}}
		/>
	</PanelRow>
);

registerBlockType(metadata.name, {
	icon: getLogo(),

	transforms: {
		from: [
			{
				type: 'shortcode',
				tag: 'directorist_search_listing',
				attributes: getAttsForTransform(metadata.attributes),
			},
		],
	},

	edit({ attributes, setAttributes }) {
		const [shouldRender, setShouldRender] = useState(true);

		let {
			show_title_subtitle,
			search_bar_title,
			search_bar_sub_title,
			more_filters_button,
			more_filters_text,
			reset_filters_button,
			apply_filters_button,
			reset_filters_text,
			apply_filters_text,
			more_filters_display,
			logged_in_user_only,
			directory_type,
			default_directory_type,
			show_popular_category,
			align,
			title_align,
			type_nav_display,
			search_button_text,
			category_align,
		} = attributes;

		let oldTypes = directory_type ? directory_type.split(',') : [];

		return (
			<Fragment>
				<InspectorControls>
					<PanelBody
						title={__('General', 'directorist')}
						initialOpen={true}
					>
						<h3>
							<strong>Heading Area</strong>
						</h3>
						<ToggleControl
							label={__('Display Header Section', 'directorist')}
							checked={show_title_subtitle}
							onChange={(newState) =>
								setAttributes({ show_title_subtitle: newState })
							}
						/>
						{show_title_subtitle && (
							<>
								<TextControl
									label={__('Title', 'directorist')}
									type="text"
									value={search_bar_title}
									onChange={(newState) =>
										setAttributes({
											search_bar_title: newState,
										})
									}
								/>
								<TextControl
									label={__('Subtitle', 'directorist')}
									type="text"
									value={search_bar_sub_title}
									onChange={(newState) =>
										setAttributes({
											search_bar_sub_title: newState,
										})
									}
								/>
							</>
						)}
						<ToggleGroupControl
							label={__(
								'Title & subtitle Alignment',
								'directorist'
							)}
							value={title_align}
							onChange={(value) =>
								setAttributes({ title_align: value })
							}
							isBlock
						>
							<ToggleGroupControlOption
								value="left"
								label={__('Left', 'directorist')}
							/>
							<ToggleGroupControlOption
								value="center"
								label={__('Center', 'directorist')}
							/>
							<ToggleGroupControlOption
								value="right"
								label={__('Right', 'directorist')}
							/>
						</ToggleGroupControl>
						<Divider />

						<h3>
							<strong>Directory Type Area</strong>
						</h3>
						{isMultiDirectoryEnabled() && (
							<TypesControl
								shouldRender={shouldRender}
								selected={oldTypes}
								showDefault={true}
								defaultType={default_directory_type}
								onDefaultChange={(value) =>
									setAttributes({
										default_directory_type: value,
									})
								}
								onChange={(types) => {
									setAttributes({
										directory_type: types.join(','),
									});
									if (types.length === 1) {
										setAttributes({
											default_directory_type: types[0],
										});
									}
									setShouldRender(false);
								}}
							/>
						)}
						<ToggleGroupControl
							label={__('Type Alignment', 'directorist')}
							value={align}
							onChange={(value) =>
								setAttributes({ align: value })
							}
							isBlock
						>
							<ToggleGroupControlOption
								value="start"
								label={__('Left', 'directorist')}
							/>
							<ToggleGroupControlOption
								value="center"
								label={__('Center', 'directorist')}
							/>
							<ToggleGroupControlOption
								value="end"
								label={__('Right', 'directorist')}
							/>
						</ToggleGroupControl>
						<ToggleGroupControl
							label={__('Icon Position', 'directorist')}
							value={type_nav_display}
							onChange={(value) =>
								setAttributes({ type_nav_display: value })
							}
							isBlock
						>
							<ToggleGroupControlOption
								value="column"
								label="↑"
							/>
							<ToggleGroupControlOption
								value="column-reverse"
								label="↓"
							/>
							<ToggleGroupControlOption value="row" label="←" />
							<ToggleGroupControlOption
								value="row-reverse"
								label="→"
							/>
						</ToggleGroupControl>
						<Divider />

						<h3>
							<strong>Search Form</strong>
						</h3>
						<TextControl
							label={__('Search Button Label', 'directorist')}
							value={search_button_text}
							onChange={(newState) =>
								setAttributes({ search_button_text: newState })
							}
						/>
						<ToggleControl
							label={__('Enable Advanced Filters', 'directorist')}
							checked={more_filters_button}
							onChange={(newState) =>
								setAttributes({ more_filters_button: newState })
							}
						/>
						{/* Additional fields omitted here for brevity */}
						<Divider />

						<h3>
							<strong>Popular Categories</strong>
						</h3>
						<ToggleControl
							label={__(
								'Enable Popular Categories',
								'directorist'
							)}
							checked={show_popular_category}
							onChange={(newState) =>
								setAttributes({
									show_popular_category: newState,
								})
							}
						/>
						<Divider />

						<ToggleControl
							label={__('Enable Authentication', 'directorist')}
							checked={logged_in_user_only}
							onChange={(newState) =>
								setAttributes({ logged_in_user_only: newState })
							}
						/>
					</PanelBody>
				</InspectorControls>

				<div
					{...useBlockProps({
						className:
							'directorist-content-active directorist-w-100',
					})}
				>
					<ServerSideRender
						block={metadata.name}
						attributes={attributes}
						LoadingResponsePlaceholder={Placeholder}
					/>
				</div>
			</Fragment>
		);
	},
});
