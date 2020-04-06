// GLOBAL VARIABLES
var iframe;
var iframeLoaded = false;
var documentChild;
var childWindow;

// IDs
var user_ID;
var device_ID;
var page_ID;
var page_URL;
var remote_URL;
var page_type = "url";
var page_width = 1440;
var page_height = 900;
var realPageWidth = page_width;
var realPageHeight = page_height;
var page_ready = false;
var page_redirected = false;
var page_colors = {};
var colorsSorted = [];

// Notifications
var Notifications = {};
var notificationTime = 3000;
var notificationTimeout;



var elementOriginalPositions = {};



// HTML Element Index
var fileIndexed = false;
var indexCount = 0;
var elementCount = 0;
var easy_html_elements = [
    "A",
	"B",
	"I",
	"U",
	"EM",
	"STRONG",
	"STRIKE",
	"SMALL",
	"TEXTAREA",
	"LABEL",
	"BUTTON",
	"TIME",
	"DATE",
	"ADDRESS",
	"BLOCKQUOTE",
	"P",
	"DIV",
	"SPAN",
	"LI",
	"H1",
	"H2",
	"H3",
	"H4",
	"H5",
	"H6",
	"TH",
	"TD"
];
var easy_with_br = easy_html_elements;
easy_with_br.push("BR");
easy_with_br.push("IMG");
easy_with_br.push("SVG");
easy_with_br.push("PATH");

// Focus Variables
var focused_element,
	focused_element_children,
	focused_element_grand_children,
	focused_element_index,
	focused_element_text,
	focused_element_pin,
	focused_element_has_live_pin,
	focused_element_editable,
	focused_element_edited_parents,
	focused_element_has_edited_child,
	focused_element_tagname;

// Activator Pin
var activator;
var cursorActive;
var cursorWasActive;

// PINS
var pinModes = {
	'live' 			: 'Content and View Changes',
	'style' 		: 'View Changes',
	'comment' 		: 'Comment',
	'private-live'	: 'Private Content and View Changes',
	'private'		: 'Private View Changes',
	'browse'		: 'Browse Mode'
};

// Pin Mode Selector
var pinTypeSelector;
var pinTypeSelectorOpen;

// Detect initial cursor mode
var cursor;
var currentCursorType = "style";

var currentPinType = "live";
var currentPinPrivate = 0;
var currentPinLabel = pinModes[currentPinType];
var currentPinTypeWas;
var currentPinPrivateWas;
var shifted = false;
var shiftToggle = false;

var currentPinNumber = 1;

// Mouse
var offset = 0;
var mouseInTheFrame = false;
var screenX = 0;
var screenY = 0;
var containerX = 0;
var containerY = 0;
var screenWidth = 10;
var screenHeight = 10;

// Hovers
var hoveringText = false;
var hoveringImage = false;
var hoveringButton = false;
var hoveringPin = false;
var focusedPin = null;

// Scrolls
var scrollOffset_top = 0;
var scrollOffset_left = 0;
var oldScrollOffset_top = 0;
var oldScrollOffset_left = 0;
var scrollX;
var scrollY;
var scrollOnPin = false;

// Initial Scale
var maxWidth = 0;
var maxHeight = 0;
var iframeScale = 1;
var iframeWidth = 0;
var iframeHeight = 0;

// Pin Window
var pinWindowOpen = false;
var pinWindowWasOpen = false;
var pinWindowWidth = 350;
var pinWindowHeight = 515;
var selectionFromContentEditor = false;
var contentEditor;

// Pins
var Pins = {};
var pinDragging = false;
var autoRefreshTimer;
var autoRefreshInterval = 5000;
var autoRefreshRequest = null;
var pinsListOpen = false;
var pinAnimation, pinAnimationTimeout;
var commentsGetRequest = null;
var removePinProcess = [];
var openPin = null;
var hiddenElementOffsets = {};
var pinSize = 35;

// Limitations
var currentAllowed = 0;
var currentLimitLabel = "";

// When document is ready, fill the variables
$(function() {

	activator = $('.current-mode');
	cursorActive = activator.data('pin-type') != "browse";
	cursorWasActive = cursorActive;

	realPageWidth = page_width = maxWidth = iframeWidth = parseInt( $('#the-page').attr('width') );
	realPageHeight = page_height = maxHeight = iframeHeight = parseInt( $('#the-page').attr('height') );

	pinWindowWidth = pinWindow().outerWidth();
	pinWindowHeight = pinWindow().outerHeight();

	pinTypeSelector = $('.pin-type-selector');
	pinTypeSelectorOpen = pinTypeSelector.parent().hasClass('selector-open');

	cursor = $('.mouse-cursor');

	currentPinType = activator.data('pin-type');
	currentPinPrivate = activator.data('pin-private');

	currentPinNumber = $('#pins > pin').length + 1;

	pinsListOpen = $('#top-bar .pins').hasClass('open');

});