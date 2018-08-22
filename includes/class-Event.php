<?php
# Linux Day 2016 - Event
# Copyright (C) 2016, 2017, 2018 Valerio Bozzolan, Ludovico Pavesi, Linux Day Torino
#
# This program is free software: you can redistribute it and/or modify
# it under the terms of the GNU Affero General Public License as published by
# the Free Software Foundation, either version 3 of the License, or
# (at your option) any later version.
#
# This program is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU Affero General Public License for more details.
#
# You should have received a copy of the GNU Affero General Public License
# along with this program.  If not, see <http://www.gnu.org/licenses/>.

trait EventTrait {

	/**
	 * Get event ID
	 *
	 * @return int
	 */
	public function getEventID() {
		return $this->nonnull( Event::ID );
	}

	/**
	 * Get event UID
	 *
	 * @return string
	 */
	public function getEventUID() {
		return $this->get( Event::UID );
	}

	/**
	 * Get localized event title
	 *
	 * @return string
	 */
	public function getEventTitle() {
		return __( $this->get( Event::TITLE ) );
	}

	/**
	 * Get localized event subtitle
	 *
	 * @return string
	 */
	public function getEventSubtitle() {
		return __( $this->get( Event::SUBTITLE ) );
	}

	/**
	 * Get an human event start date
	 *
	 * @return string
	 */
	public function getEventHumanStart() {
		return HumanTime::diff( $this->get( Event::START ) );
	}

	/**
	 * Get an human event end date
	 *
	 * @return string
	 */
	public function getEventHumanEnd() {
		return HumanTime::diff( $this->get( Event::END ) );
	}

	/**
	 * Get formatted event start date
	 *
	 * @return string
	 */
	public function getEventStart( $format = 'Y-m-d H:i:s' ) {
		return $this->get( Event::START )->format( $format );
	}

	/**
	 * When formatted event end date
	 *
	 * @return string
	 */
	public function getEventEnd( $format = 'Y-m-d H:i:s' ) {
		return $this->get( Event::END )->format( $format );
	}

	/**
	 * It has an event image?
	 *
	 * @return bool
	 */
	public function hasEventImage() {
		return null !== $this->get( Event::IMAGE );
	}

	function getEventImage($base = URL) {
		return site_page( $this->get( Event::IMAGE ) , $base );
	}

	/**
	 * It has event description?
	 *
	 * @return bool
	 */
	public function hasEventDescription() {
		return null !== $this->get( 'event_description' );
	}

	/**
	 * It has an event abstract?
	 *
	 * @return bool
	 */
	public function hasEventAbstract() {
		return null !== $this->get( 'event_abstract' );
	}

	/**
	 * It has an event note?
	 *
	 * @return bool
	 */
	function hasEventNote() {
		return null !== $this->get( 'event_note' );
	}

	/**
	 * Get the event description
	 *
	 * @return string
	 */
	public function getEventDescription() {
		return $this->get( 'event_description' );
	}

	/**
	 * Get the event abstract
	 *
	 * @return string
	 */
	public function getEventAbstract() {
		return $this->get('event_abstract');
	}

	/**
	 * Get the event note
	 *
	 * @return string
	 */
	public function getEventNote() {
		return $this->get('event_note');
	}

	function getEventDescriptionHTML($args = []) {
		return Markdown::parse( __( $this->getEventDescription() ), $args );
	}

	function getEventAbstractHTML($args = []) {
		return Markdown::parse( __( $this->getEventAbstract() ), $args );
	}

	function getEventNoteHTML($args = []) {
		return Markdown::parse( __( $this->getEventNote() ), $args );
	}

	/**
	 * Factory Users by this event
	 *
	 * @return Query
	 */
	public function factoryUserByEvent() {
		return User::factoryByEvent( $this->getEventID() );
	}

	/**
	 * Factory Sharables by this event
	 *
	 * @return Query
	 */
	public function factorySharebleByEvent() {
		return Sharable::factoryByEvent( $this->getEventID() );
	}

	/**
	 * You can edit this event?
	 *
	 * @return bool
	 */
	public function isEventEditable() {
		return has_permission('edit-events');
	}

	/**
	 * Insert subscription if not exists
	 */
	function addSubscription($email) {
		$exists = Subscription::getStandardQuery( $email, $this->getEventID() )->getRow('Subscription');

		$exists || Subscription::insert( $email, $this->getEventID() );

		return $exists;
	}

	/**
	 * Are event subscriptions available?
	 *
	 * @return bool
	 */
	public function areEventSubscriptionsAvailable() {
		return $this->get( 'event_subscriptions' ) && ! $this->isEventPassed();
	}

	/**
	 * Is event passed?
	 *
	 * @return bool
	 */
	public function isEventPassed() {
		$now = new DateTime('now');
		return $now->diff( $this->get('event_end') )->invert === 1;
	}

	/**
	 * Normalize an Event object
	 */
	protected function normalizeEvent() {
		$this->integers( Event::ID );
		$this->datetimes(
			Event::START,
			Event::END
		);
		$this->booleans('event_subscriptions');
	}
}

/**
 * An Event can be a talk or a lesson etc.
 */
class Event extends Queried {

	use EventTrait;

	/**
	 * Database table name
	 */
	const T = 'event';

	/**
	 * ID column name
	 */
	const ID = 'event_ID';

	/**
	 * UID column name
	 */
	const UID = 'event_uid';

	/**
	 * Title column name
	 */
	const TITLE = 'event_title';

	/**
	 * Subtitle column name
	 */
	const SUBTITLE = 'event_subtitle';

	/**
	 * Image column name
	 */
	const IMAGE = 'event_img';

	/**
	 * Start column name
	 */
	const START = 'event_start';

	/**
	 * End column name
	 */
	const END = 'event_end';

	/**
	 * Maximum UID length
	 *
	 * @override
	 */
	const MAXLEN_UID = 100;

	/**
	 * Constructor
 	 */
	public function __construct() {
		$this->normalizeEvent();
	}
}
