<?php

class TestHookTimer extends WP_UnitTestCase {

	private $action = 'hook-timer-test';
	private $filter_one = 'hook-timer-filter-one';
	private $filter_two = 'hook-timer-filter-two';

	public function setUp() {
		$this->ht = WP_HookTimer::getInstance();
		$this->did_action = false;

		add_action( $this->action, array( $this, 'hookAction' ) );
		add_filter( $this->filter_one, array( $this, 'hookFilterOne' ) );
		add_filter( $this->filter_two, array( $this, 'hookFilterTwo' ) );
	}

	public function tearDown() {
		remove_filter( $this->filter_two, array( $this, 'hookFilterTwo' ) );
		remove_filter( $this->filter_one, array( $this, 'hookFilterOne' ) );
		remove_action( $this->action, array( $this, 'hookAction' ) );

		unset( $this->did_action );
		unset( $this->ht );
	}

	public function hookAction( $args ) {
		$this->did_action = true;
	}

	public function hookFilterOne( $data ) {
		$data = '1' . $data . '1';
		return apply_filters( $this->filter_two, $data );
	}

	public function hookFilterTwo( $data ) {
		return '2' . $data . '2';
	}

	public function testConstructor() {
		$this->assertTrue( $this->ht instanceof WP_HookTimer );
	}

	public function testTestAction() {
		do_action( $this->action );

		$this->assertTrue( $this->did_action );
	}

	public function testNoTestActionRemainsFalse() {
		$this->assertFalse( $this->did_action );
	}

	public function testGetTimesByHook() {
		do_action( $this->action );

		$result = $this->ht->get_times_by_hook( $this->action );
		$this->assertNotEmpty( $result );
	}

	public function testTestFilters() {
		$data = 'test';

		$result = apply_filters( $this->filter_one, $data );

		$this->assertEquals( '21test12', $result );

		$result_one = $this->ht->get_times_by_hook( $this->filter_one );
		$result_two = $this->ht->get_times_by_hook( $this->filter_two );

		$this->assertNotEmpty( $result_one );
		$this->assertNotEmpty( $result_two );
	}

	public function filterTimingA( $data ) {
		sleep( 1 );

		return apply_filters( 'filter_b', $data );
	}

	public function filterTimingB( $data ) {
		sleep( 2 );
	}

	public function testFilterTiming() {
		add_filter( 'filter_a', array( $this, 'filterTimingA' ) );
		add_filter( 'filter_b', array( $this, 'filterTimingB' ) );

		apply_filters( 'filter_a', '' );

		$result = $this->ht->get_times_by_hook( 'filter_a' );
		$this->assertEquals( 3, round( $result[ 0 ][ 0 ] ) );

		$result = $this->ht->get_times_by_hook( 'filter_b' );
		$this->assertEquals( 2, round( $result[ 0 ][ 0 ] ) );

		remove_filter( 'filter_b', array( $this, 'filterTimingB' ) );
		remove_filter( 'filter_a', array( $this, 'filterTimingA' ) );
	}

}
