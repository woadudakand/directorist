export default {
	props: {
		sectionId: {
			type: [String, Number],
			default: '',
		},
		fieldId: {
			type: [String, Number],
			default: '',
		},
		fieldKey: {
			type: [String, Number],
			default: '',
		},
		root: {
			required: false,
		},
		mapAtts: {
			required: false,
		},
		filters: {
			required: false,
		},
		data: {
			required: false,
		},
		exportAs: {
			required: false,
		},
		theme: {
			type: String,
			default: 'default',
		},
		confirmBeforeChange: {
			required: false,
		},
		confirmationModal: {
			required: false,
		},
		optionFields: {
			required: false,
		},
		cachedData: {
			required: false,
		},
		dataOnChange: {
			required: false,
		},
		saveOptionData: {
			default: false,
		},
		changeIf: {
			required: false,
		},
		showIf: {
			required: false,
		},
		show_if: {
			required: false,
		},
		type: {
			type: String,
			default: '',
		},
		icon: {
			type: String,
			default: '',
		},
		label: {
			type: [String, Number],
			default: '',
		},
		labelType: {
			type: [String],
			default: 'span',
		},
		disable: {
			type: Boolean,
			default: false,
		},
		shortcodes: {
			type: [Array, String],
			default: '',
		},
		buttonLabel: {
			type: String,
			default: '',
		},
		buttonClass: {
			type: String,
			default: '',
		},
		copyButtonLabel: {
			type: String,
			default: '<i class="far fa-copy"></i>',
		},
		exportFileName: {
			type: String,
			default: 'data',
		},
		restorData: {
			required: false,
		},
		buttonLabelOnProcessing: {
			type: String,
			default: '',
		},
		action: {
			type: String,
			default: '',
		},
		url: {
			type: String,
			default: '',
		},
		openInNewTab: {
			type: Boolean,
			default: true,
		},
		title: {
			type: [String],
			default: '',
		},
		description: {
			type: [String],
			default: '',
		},
		id: {
			type: [String, Number],
			default: '',
		},
		name: {
			type: [String, Number],
			default: '',
		},
		multi_directory_status: {
			type: String,
			default: '',
		},
		schema: {
			type: String,
			default: '',
		},
		value: {
			default: '',
		},
		options: {
			required: false,
		},
		optionsSource: {
			required: false,
		},
		showDefaultOption: {
			type: Boolean,
			default: false,
		},
		defaultOption: {
			type: Object,
			required: false,
		},
		placeholder: {
			type: [String, Number],
			default: '',
		},
		infoTextForNoOption: {
			type: String,
			default: 'Nothing available',
		},
		cols: {
			type: [String, Number],
			default: '30',
		},
		rows: {
			type: [String, Number],
			default: '10',
		},
		min: {
			type: [String, Number],
			default: undefined,
		},
		max: {
			type: [String, Number],
			default: undefined,
		},
		step: {
			type: [String, Number],
			default: undefined,
		},
		componets: {
			required: false,
		},
		defaultImg: {
			required: false,
		},
		selectButtonLabel: {
			type: String,
			default: 'Select',
		},
		changeButtonLabel: {
			type: String,
			default: 'Change',
		},
		prepareExportFileFrom: {
			type: String,
			default: '',
		},
		rules: {
			required: false,
		},
		validationState: {
			required: false,
		},
		validation: {
			required: false,
		},
		nonce: {
			required: false,
		},
		preview: {
			required: false,
		},
		editor: {
			required: false,
		},
		editorID: {
			required: false,
		},
	},
};
