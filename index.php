<?php
/**
 * Copyright (c) 2014-present, Facebook, Inc. All rights reserved.
 *
 * You are hereby granted a non-exclusive, worldwide, royalty-free license to
 * use, copy, modify, and distribute this software in source code or binary
 * form for use in connection with the web services and APIs provided by
 * Facebook.
 *
 * As with any software that integrates with the Facebook platform, your use
 * of this software is subject to the Facebook Developer Principles and
 * Policies [http://developers.facebook.com/policy/]. This copyright notice
 * shall be included in all copies or substantial portions of the software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL
 * THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING
 * FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER
 * DEALINGS IN THE SOFTWARE.
 *
 */

// Configurations
$access_token = 'EAAEIw2VSmIkBAFZAdTOKjt0H0wPxe7Qkh1t4K08ZBorhl1NantDvwZAZCDRxQji8pSTkCvVWq3gJloPsG3nBywjN0cFseuP9Clb1bmPGOXAjd36IHsvYoEhHE1HhIsab9UYRj3Xt2ZAYHgqljUIZCvZCY7vcwiJgql86H64GKvnDWSdZADRZB2QlLjtaQaQ1UwBUygFLFbBulMHRWZAcRltvWM';
$app_id = '291110288267401';
$app_secret = '69fc77650e02a05f1d260a460ea34b1f';
// should begin with "act_" (eg: $account_id = 'act_1234567890';)
$account_id = 'act_1166285790162852';
define('SDK_DIR', __DIR__ . '/facebook-php-ads-sdk'); // Path to the SDK directory
$loader = include SDK_DIR.'/vendor/autoload.php';
date_default_timezone_set('America/Los_Angeles');
// Configurations - End

if(is_null($access_token) || is_null($app_id) || is_null($app_secret)) {
	throw new \Exception(
		'You must set your access token, app id and app secret before executing'
	);
}

if (is_null($account_id)) {
	throw new \Exception(
		'You must set your account id before executing');
}

use FacebookAds\Api;

Api::init($app_id, $app_secret, $access_token);


/**
 * Step 1 Read the AdAccount (optional)
 */
use FacebookAds\Object\AdAccount;
use FacebookAds\Object\Fields\AdAccountFields;


$account = (new AdAccount($account_id))->read(array(
	AdAccountFields::ID,
	AdAccountFields::NAME,
	AdAccountFields::ACCOUNT_STATUS,
));

echo "\nUsing this account: ";
echo $account->id."\n";

// Check the account is active
if($account->{AdAccountFields::ACCOUNT_STATUS} !== 1) {
	throw new \Exception(
		'This account is not active');
}

/**
 * Step 2 Create the Campaign
 */
use FacebookAds\Object\Campaign;
use FacebookAds\Object\Fields\CampaignFields;
use FacebookAds\Object\Values\AdAccountTargetingUnifiedObjectiveValues;

$campaign  = new Campaign(null, $account->id);
$campaign->setData(array(
	CampaignFields::NAME => 'My First Campaign',
	CampaignFields::OBJECTIVE => AdAccountTargetingUnifiedObjectiveValues::LINK_CLICKS,
));

$campaign->validate()->create(array(
	Campaign::STATUS_PARAM_NAME => Campaign::STATUS_PAUSED,
));
echo "Campaign ID:" . $campaign->id . "\n";


/**
 * Step 4 Create the AdSet
 */
use FacebookAds\Object\AdSet;
//use FacebookAds\Object\AdAccount;

$fields = array(
);
$params = array(
	'name' => 'A CPA Ad Set',
	'campaign_id' => $campaign->id,
	'daily_budget' => '5000',
	'start_time' => (new \DateTime("+1 week"))->format(\DateTime::ISO8601),
	'end_time' => (new \DateTime("+2 week"))->format(\DateTime::ISO8601),
	'billing_event' => 'IMPRESSIONS',
	'optimization_goal' => 'REACH',
	'bid_amount' => '1000',
	'targeting' => array('geo_locations' => array('countries' => array('US'))),
	'user_os' => 'iOS',
	'publisher_platforms' => 'facebook',
	'device_platforms' => 'mobile',
);
$adset = (new AdAccount($account->id))->createAdSet(
	$fields,
	$params
);
echo "Adset ID:" . $adset->id . "\n";

/**
 * Step 5 Create an AdImage
 */
use FacebookAds\Object\AdImage;
use FacebookAds\Object\Fields\AdImageFields;

$image = new AdImage(null, $account->id);
$image->{AdImageFields::FILENAME}
       = SDK_DIR.'/test/misc/image.png';

$image->create();
echo 'Image Hash: '.$image->hash . "\n";

/**
 * Step 6 Create an AdCreative
 */



use FacebookAds\Object\AdCreative;
use FacebookAds\Object\Fields\AdCreativeFields;
use FacebookAds\Object\Fields\AdCreativeLinkDataFields;
use FacebookAds\Object\Fields\AdCreativeObjectStorySpecFields;
use FacebookAds\Object\Fields\AdCreativeLinkDataChildAttachmentFields;
use FacebookAds\Object\AdCreativeLinkDataChildAttachment;
use FacebookAds\Object\AdCreativeLinkData;
use FacebookAds\Object\AdCreativeObjectStorySpec;
//
//$product = (new AdCreativeLinkDataChildAttachment())->setData(array(
//	AdCreativeLinkDataChildAttachmentFields::LINK =>
//		'https://www.link.com/product',
//	AdCreativeLinkDataChildAttachmentFields::NAME => 'Test Campaign ',
//	AdCreativeLinkDataChildAttachmentFields::DESCRIPTION => 'Test Description',
//	AdCreativeLinkDataChildAttachmentFields::IMAGE_HASH => $image->hash,
//
//));
//$product->create();
//
//$link_data = new AdCreativeLinkData();
//$link_data->setData(array(
//	AdCreativeLinkDataFields::LINK => 'https://www.google.com/',
//	AdCreativeLinkDataFields::CHILD_ATTACHMENTS => array(
//		$product->id,
//	),
//));
//
//$object_story_spec = new AdCreativeObjectStorySpec();
//$object_story_spec->setData(array(
//	AdCreativeObjectStorySpecFields::PAGE_ID => '392761521486556',
//	AdCreativeObjectStorySpecFields::LINK_DATA => $link_data,
//));

//$creative = new AdCreative(null, $account->id);
//$creative->setData(array(
//	AdCreativeFields::NAME => 'Sample Creative',
////	AdCreativeFields::OBJECT_STORY_SPEC => $object_story_spec,
//));
//
//$creative->create();
$fields = array(
);
$params = array(
	'object_story_id' => '392761521486556',
);

$creative = (new AdAccount($account->id))->createAdCreative(
	$fields,
	$params
);
echo 'Creative ID: '.$creative->id . "\n";


/**
 * Step 7 Create an Ad
 */
use FacebookAds\Object\Ad;
use FacebookAds\Object\Fields\AdFields;

$ad = new Ad(null, $account->id);
$ad->setData(array(
	AdFields::CREATIVE =>
		array('creative_id' => $creative->id),
	AdFields::NAME => 'My First Ad',
	AdFields::ADSET_ID => $adset->id,
));

$ad->create();
echo 'Ad ID:' . $ad->id . "\n";