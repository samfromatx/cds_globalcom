// JavaScript Document
/*****
Dynamic Javascript Breadcrumb Navigation by Adam DuVander
http://duvinci.com/projects/javascript/crumbs/

Released under Creative Commons License:
http://creativecommons.org/licenses/by/2.5/
*****/
var crumbsep = " &raquo; ";
var precrumb = "";
var postcrumb = "";
var sectionsep = "/";
var rootpath = "/perspectives/"; // Use "/" for root of domain.
var rootname = "Home";

var ucfirst = 0; // if set to 1, makes "directory" default to "Directory"

var objurl = new Object;
objurl['topics'] = 'All Topics';

// Grab the page's url and break it up into directory pieces
var pageurl = (new String(document.location));
var protocol = pageurl.substring(0, pageurl.indexOf("//") + 2);
pageurl = pageurl.replace(protocol, ""); // remove protocol from pageurl
var rooturl = pageurl.substring(0, pageurl.indexOf(rootpath) + rootpath.length); // find rooturl
if (rooturl.charAt(rooturl.length - 1) == "/") //remove trailing slash
	{
	  rooturl = rooturl.substring(0, rooturl.length - 1);
	}
pageurl = pageurl.replace(rooturl, ""); // remove rooturl fro pageurl
if (pageurl.charAt(0) == '/') // remove beginning slash
	{
	  pageurl = pageurl.substring(1, pageurl.length);
	}

var page_ar = pageurl.split(sectionsep);
var currenturl = protocol + rooturl;
var allbread = precrumb + "<a href=\"" + currenturl + "\">" + rootname + "</a>" + postcrumb; // start with root

for (i=0; i < page_ar.length-1; i++) {
	var displayname = "";
	currenturl += "/" + page_ar[i];
	if (objurl[page_ar[i]]) {
		displayname = objurl[page_ar[i]];
	}
	else {
		if (ucfirst == 1) {
			displayname = page_ar[i].charAt(0).toUpperCase() + page_ar[i].substring(1);
		}
		else {
			displayname = page_ar[i];
			if (displayname == "featured") { displayname = "Features"; }
			else if (displayname == "news") { displayname = "News &amp; Notes"; }
			else if (displayname == "boone") { displayname = "Boone"; }
			else if (displayname == "east_washington") { displayname = "East Washington"; }
			else if (displayname == "redoak") { displayname = "Red Oak"; }
			else if (displayname == "tipton") { displayname = "Tipton"; }
			else if (displayname == "west_des_moines") { displayname = "West Des Moines"; }
			else if (displayname == "harlan") { displayname = "Harlan"; }
			else if (displayname == "blog") { displayname = "Blog"; }
			else if (displayname == "blogs") { displayname = "Blogs"; }
			else if (displayname == "us") { displayname = "United States"; }
			else if (displayname == "uk") { displayname = "United Kingdom"; }
			else if (displayname == "index.php") { displayname = "Blog Home"; }
			else if (displayname == "interns") { displayname = "Interns"; }
			else if (displayname == "careers") { displayname = "Careers"; }
			else if (displayname == "internships") { displayname = "Internships"; }
			else if (displayname == "postings") { displayname = "Internship Postings"; }
			else if (displayname == "listings") { displayname = "Job Listings"; }
			else if (displayname == "contact") { displayname = "Contact"; }
			else if (displayname == "employeemessages") { displayname = "Employee Messages"; }
			else if (displayname == "industries") { displayname = "Industries"; }
			else if (displayname == "businesstobusiness") { displayname = "Business-to-Business"; }
			else if (displayname == "consumer") { displayname = "Consumer"; }
			else if (displayname == "publishing") { displayname = "Publishing"; }
			else if (displayname == "resources") { displayname = "Resources"; }
			else if (displayname == "newsroom") { displayname = "Newsroom"; }
			else if (displayname == "articles") { displayname = "Articles"; }
			else if (displayname == "casestudies") { displayname = "Case Studies"; }
			else if (displayname == "newsreleases") { displayname = "News Releases"; }
			else if (displayname == "testimonials") { displayname = "Testimonials"; }
			else if (displayname == "whitepapers") { displayname = "White Papers"; }
			else if (displayname == "forwardreports") { displayname = "Forward Reports"; }
			else if (displayname == "search") { displayname = "Site Search"; }
			else if (displayname == "seminar") { displayname = "CDS Global Strategic Partner Seminar"; }
			else if (displayname == "solutions") { displayname = "Solutions"; }
			else if (displayname == "customer_service") { displayname = "Customer Service"; }
			else if (displayname == "ecommerce") { displayname = "eCommerce"; }
			else if (displayname == "integrated_marketing") { displayname = "Integrated Marketing"; }
			else if (displayname == "subscription_fulfillment") { displayname = "Subscription Fulfillment"; }
			else if (displayname == "transaction_management") { displayname = "Transaction Management"; }
			else if (displayname == "t3st1ng") { displayname = "Testing"; }
			else if (displayname == "en") { displayname = "English"; }
			else if (displayname == "fr") { displayname = "French"; }
			else if (displayname == "registration") { displayname = "Registration"; }
			else if (displayname == "register") { displayname = "Registration"; }
			else if (displayname == "cdsglobalsummit2012") { displayname = "Summit 2012"; }
			else if (displayname == "webinars") { displayname = "Webinars"; }
			else if (displayname == "webinar") { displayname = "Webinar"; }
			else if (displayname == "articles") { displayname = "Industry Articles"; }
			else if (displayname == "sales") { displayname = "Sales"; }
			else if (displayname == "presentations") { displayname = "Presentations"; }
			else if (displayname == "digital") { displayname = "Digital"; }
			else if (displayname == "ondemand") { displayname = "Cross-Media Communications"; }
			else if (displayname == "cross-media_communications") { displayname = "Cross-Media Communications"; }
			else if (displayname == "order_management") { displayname = "Order Management"; }
			else if (displayname == "remittance_lockbox") { displayname = "Remittance &amp; Lockbox"; }
			else if (displayname == "remittance_processing") { displayname = "Remittance Processing"; }
			else if (displayname == "lockbox_processing") { displayname = "Lockbox Processing"; }
			else if (displayname == "product_fulfillment") { displayname = "Product Fulfillment"; }
			else if (displayname == "industries") { displayname = "Industries"; }
			else if (displayname == "magazines_and_media") { displayname = "Magazines &amp; Media"; }
			else if (displayname == "finance_insurance") { displayname = "Financial &amp; Insurance"; }
			else if (displayname == "nonprofit") { displayname = "Nonprofit"; }
			else if (displayname == "utilities") { displayname = "Utilities"; }
			else if (displayname == "microsites") { displayname = "Microsites"; }
			else if (displayname == "email") { displayname = "Email"; }
			else if (displayname == "video") { displayname = "Video"; }
			else if (displayname == "forms") { displayname = "Forms"; }
			else if (displayname == "anniversary") { displayname = "Anniversary"; }
			else if (displayname == "2012") { displayname = "2012"; }
			else if (displayname == "november") { displayname = "November"; }
			else if (displayname == "2013") { displayname = "2013"; }
			else if (displayname == "archives") { displayname = "Archives"; }
			else if (displayname == "january") { displayname = "January"; }
			else if (displayname == "march") { displayname = "March"; }
			else if (displayname == "polls") { displayname = "Polls"; }
			else if (displayname == "may") { displayname = "May"; }
			}
	}
	allbread += crumbsep + precrumb + "<a href=\"" + currenturl + "\">" + displayname + "</a>" + postcrumb;
}
document.write(allbread);