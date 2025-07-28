import {
	useBlockProps,
	InspectorControls,
	BlockControls,
} from '@wordpress/block-editor';
import { registerBlockType, createBlock } from '@wordpress/blocks';
import ServerSideRender from '@wordpress/server-side-render';
import { Fragment, useState } from '@wordpress/element';
import { __ } from '@wordpress/i18n';
import {
	LocationControl,
	CategoryControl,
	TagsControl,
	ListingControl,
	TypesControl,
} from './../controls';

import { list, grid, mapMarker } from '@wordpress/icons';

import {
	PanelBody,
	PanelRow,
	SelectControl,
	ToggleControl,
	TextControl,
	ToolbarGroup,
	ToolbarButton,
	__experimentalToggleGroupControl as ToggleGroupControl,
	__experimentalToggleGroupControlOption as ToggleGroupControlOption,
} from '@wordpress/components';

import {
	getAttsForTransform,
	isMultiDirectoryEnabled,
	getPlaceholder,
} from './../functions';
import metadata from './block.json';
import getLogo from './../logo';

const Placeholder = () => getPlaceholder('listing-grid');

const SectionTitle = ({ children }) => (
	<div style={{ marginTop: '0px', marginBottom: '8px' }}>
		<h3 style={{ fontSize: '14px', fontWeight: 600, margin: 0 }}>
			{children}
		</h3>
	</div>
);
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

	supports: {
		html: false,
	},

	transforms: {
		from: [
			{
				type: 'shortcode',
				tag: 'directorist_all_listing',
				attributes: getAttsForTransform(metadata.attributes),
			},
			{
				type: 'block',
				blocks: [
					'directorist/category',
					'directorist/location',
					'directorist/tag',
				],
				transform: (attributes) => {
					return createBlock('directorist/all-listing', attributes);
				},
			},
		],
	},

	edit({ attributes, setAttributes }) {
		const [shouldRender, setShouldRender] = useState(true);

		let {
			view,
			_featured,
			filterby,
			orderby,
			order,
			listings_per_page,
			show_pagination,
			header,
			header_title,
			category,
			location,
			tag,
			ids,
			columns,
			featured_only,
			popular_only,
			advanced_filter,
			display_preview_image,
			logged_in_user_only,
			map_height,
			map_zoom_level,
			directory_type,
			default_directory_type,
			query_type,
			sidebar,
			align,
			type_nav_display,
		} = attributes;

		let oldLocations = location ? location.split(',') : [],
			oldCategories = category ? category.split(',') : [],
			oldTags = tag ? tag.split(',') : [],
			oldTypes = directory_type ? directory_type.split(',') : [],
			oldIds = ids ? ids.split(',').map((id) => Number(id)) : [];

		return (
			<Fragment>
				<BlockControls>
					<ToolbarGroup>
						<ToolbarButton
							isPressed={view === 'grid'}
							icon={grid}
							label={__('Grid View', 'directorist')}
							onClick={() => setAttributes({ view: 'grid' })}
						/>
						<ToolbarButton
							isPressed={view === 'list'}
							icon={list}
							label={__('List View', 'directorist')}
							onClick={() => setAttributes({ view: 'list' })}
						/>
						<ToolbarButton
							isPressed={view === 'map'}
							icon={mapMarker}
							label={__('Map View', 'directorist')}
							onClick={() => setAttributes({ view: 'map' })}
						/>
					</ToolbarGroup>
				</BlockControls>

				<InspectorControls>
					<PanelBody
						title={__('General Settings', 'directorist')}
						initialOpen={true}
					>
						<SectionTitle>
							{__('Directory Type Settings', 'directorist')}
						</SectionTitle>

						{isMultiDirectoryEnabled() ? (
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
						) : (
							''
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
								aria-label={__('Left', 'directorist')}
							/>
							<ToggleGroupControlOption
								value="center"
								label={__('Center', 'directorist')}
								aria-label={__('Center', 'directorist')}
							/>
							<ToggleGroupControlOption
								value="end"
								label={__('Right', 'directorist')}
								aria-label={__('Right', 'directorist')}
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
								aria-label={__('Default', 'directorist')}
							/>
							<ToggleGroupControlOption
								value="column-reverse"
								label="↓"
								aria-label={__('Column Reverse', 'directorist')}
							/>
							<ToggleGroupControlOption
								value="row"
								label="←"
								aria-label={__('Row', 'directorist')}
							/>
							<ToggleGroupControlOption
								value="row-reverse"
								label="→"
								aria-label={__('Row Reverse', 'directorist')}
							/>
						</ToggleGroupControl>
						<Divider />

						<SectionTitle>
							{__('Listing Configuration', 'directorist')}
						</SectionTitle>
						<ToggleControl
							label={__('Display Header', 'directorist')}
							checked={header}
							onChange={(newState) =>
								setAttributes({ header: newState })
							}
						/>
						{header ? (
							<TextControl
								label={__('Listings Found Text', 'directorist')}
								type="text"
								value={header_title}
								onChange={(newState) =>
									setAttributes({ header_title: newState })
								}
							/>
						) : null}

						<SelectControl
							label={__('Sidebar Options', 'directorist')}
							labelPosition="side"
							value={sidebar}
							options={[
								{
									label: __('Default', 'directorist'),
									value: '',
								},
								{
									label: __('Left Sidebar', 'directorist'),
									value: 'left_sidebar',
								},
								{
									label: __('Right Sidebar', 'directorist'),
									value: 'right_sidebar',
								},
								{
									label: __('No Sidebar', 'directorist'),
									value: 'no_sidebar',
								},
							]}
							onChange={(newState) =>
								setAttributes({
									sidebar: newState,
								})
							}
							className="directorist-gb-fixed-control"
						/>

						<ToggleControl
							label={__('Display Preview Image', 'directorist')}
							checked={display_preview_image}
							onChange={(newState) =>
								setAttributes({
									display_preview_image: newState,
								})
							}
						/>

						<SelectControl
							label={__('View as', 'directorist')}
							labelPosition="side"
							value={view}
							options={[
								{
									label: __('Grid', 'directorist'),
									value: 'grid',
								},
								{
									label: __('List', 'directorist'),
									value: 'list',
								},
								{
									label: __('Map', 'directorist'),
									value: 'map',
								},
							]}
							onChange={(newState) =>
								setAttributes({ view: newState })
							}
							className="directorist-gb-fixed-control"
						/>
						{view === 'grid' ? (
							<SelectControl
								label={__('Columns', 'directorist')}
								labelPosition="side"
								value={columns}
								options={[
									{
										label: __('1 Column', 'directorist'),
										value: 1,
									},
									{
										label: __('2 Columns', 'directorist'),
										value: 2,
									},
									{
										label: __('3 Columns', 'directorist'),
										value: 3,
									},
									{
										label: __('4 Columns', 'directorist'),
										value: 4,
									},
									{
										label: __('6 Columns', 'directorist'),
										value: 6,
									},
								]}
								onChange={(newState) =>
									setAttributes({
										columns: Number(newState),
									})
								}
								className="directorist-gb-fixed-control"
							/>
						) : (
							''
						)}

						{sidebar == 'no_sidebar' && header ? (
							<ToggleControl
								label={__(
									'Display Filter Button',
									'directorist'
								)}
								checked={advanced_filter}
								onChange={(newState) =>
									setAttributes({ advanced_filter: newState })
								}
							/>
						) : null}

						{view === 'map' ? (
							<TextControl
								label={__('Map Height', 'directorist')}
								type="number"
								value={map_height}
								help={__(
									'Applicable for map view only',
									'directorist'
								)}
								onChange={(newState) =>
									setAttributes({
										map_height: Number(newState),
									})
								}
								className={`directorist-gb-fixed-control ${
									view !== 'map' ? 'hidden' : ''
								}`}
							/>
						) : (
							''
						)}
						{view === 'map' ? (
							<TextControl
								label={__('Map Zoom Level', 'directorist')}
								help={__(
									'Applicable for map view only',
									'directorist'
								)}
								type="number"
								value={map_zoom_level}
								onChange={(newState) =>
									setAttributes({
										map_zoom_level: Number(newState),
									})
								}
								className="directorist-gb-fixed-control"
							/>
						) : (
							''
						)}

						<ToggleControl
							label={__(
								'Display Featured Listings Only',
								'directorist'
							)}
							checked={featured_only}
							onChange={(newState) =>
								setAttributes({ featured_only: newState })
							}
						/>

						<ToggleControl
							label={__('Display Popular Only', 'directorist')}
							checked={popular_only}
							onChange={(newState) =>
								setAttributes({ popular_only: newState })
							}
						/>

						<TextControl
							label={__('Listings Per Page', 'directorist')}
							type="number"
							value={listings_per_page}
							onChange={(newState) =>
								setAttributes({
									listings_per_page: Number(newState),
								})
							}
							className="directorist-gb-fixed-control"
							help={__(
								'Set the number of listings to show per page.',
								'directorist'
							)}
						/>
						<ToggleControl
							label={__(
								'LoggedIn User Can View Only',
								'directorist'
							)}
							checked={logged_in_user_only}
							onChange={(newState) =>
								setAttributes({
									logged_in_user_only: newState,
								})
							}
						/>

						<Divider />

						<SectionTitle>
							{__('Pagination Area', 'directorist')}
						</SectionTitle>

						<ToggleControl
							label={__('Enable Pagination', 'directorist')}
							checked={show_pagination}
							onChange={(newState) =>
								setAttributes({ show_pagination: newState })
							}
						/>
					</PanelBody>

					<PanelBody
						title={__('Query', 'directorist')}
						initialOpen={false}
					>
						<SelectControl
							label={__('Order By', 'directorist')}
							labelPosition="side"
							value={orderby}
							options={[
								{
									label: __('Title', 'directorist'),
									value: 'title',
								},
								{
									label: __('Date', 'directorist'),
									value: 'date',
								},
								{
									label: __('Random', 'directorist'),
									value: 'rand',
								},
								{
									label: __('Price', 'directorist'),
									value: 'price',
								},
							]}
							onChange={(newState) =>
								setAttributes({ orderby: newState })
							}
							className="directorist-gb-fixed-control"
						/>
						<SelectControl
							label={__('Order', 'directorist')}
							labelPosition="side"
							value={order}
							options={[
								{
									label: __('ASC', 'directorist'),
									value: 'asc',
								},
								{
									label: __('DESC', 'directorist'),
									value: 'desc',
								},
							]}
							onChange={(newState) =>
								setAttributes({ order: newState })
							}
							className="directorist-gb-fixed-control"
						/>

						<SelectControl
							label={__('Query Type', 'directorist')}
							labelPosition="side"
							value={query_type}
							options={[
								{
									label: __('Regular', 'directorist'),
									value: 'regular',
								},
								{
									label: __('Selective', 'directorist'),
									value: 'selective',
								},
							]}
							onChange={(newState) => {
								let states = {
									query_type: newState,
								};

								if (newState === 'selective') {
									states.category = '';
									states.tag = '';
									states.location = '';
								} else if (newState === 'regular') {
									states.ids = '';
								}

								setAttributes(states);
							}}
							className="directorist-gb-fixed-control"
						/>

						{query_type === 'selective' && (
							<ListingControl
								onChange={(ids) => {
									setAttributes({ ids: ids.join(',') });
								}}
								value={oldIds}
							/>
						)}

						{query_type !== 'selective' && (
							<CategoryControl
								onChange={(categories) => {
									setAttributes({
										category: categories.join(','),
									});
								}}
								value={oldCategories}
							/>
						)}

						{query_type !== 'selective' && (
							<TagsControl
								onChange={(tags) => {
									setAttributes({ tag: tags.join(',') });
								}}
								value={oldTags}
							/>
						)}

						{query_type !== 'selective' && (
							<LocationControl
								onChange={(locations) => {
									setAttributes({
										location: locations.join(','),
									});
								}}
								value={oldLocations}
							/>
						)}
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
