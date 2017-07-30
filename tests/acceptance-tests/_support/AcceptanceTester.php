<?php


/**
 * Inherited Methods
 * @method void wantToTest( $text )
 * @method void wantTo( $text )
 * @method void execute( $callable )
 * @method void expectTo( $prediction )
 * @method void expect( $prediction )
 * @method void amGoingTo( $argumentation )
 * @method void am( $role )
 * @method void lookForwardTo( $achieveValue )
 * @method void comment( $description )
 * @method \Codeception\Lib\Friend haveFriend( $name, $actorClass = null )
 *
 * @SuppressWarnings(PHPMD)
 */
class AcceptanceTester extends \Codeception\Actor {
	use _generated\AcceptanceTesterActions;

	/**
	 * Define custom actions here
	 */

	public function logOut() {
		$I = $this;
		$I->amOnPage( '/wp-login.php?action=logout' );
		$I->click( 'log out' );
		$I->wait( 1 );
	}

	/**
	 * Navigate to the specified Workflow page in the admin.
	 *
	 * @param string $page The page to visit e.g. Inbox or Status
	 */
	public function amOnWorkflowPage( $page ) {
		$I = $this;
		$I->amOnPage( '/wp-admin' );
		$I->click( 'Workflow' );
		$I->waitForText( $page, 3 );
		$I->click( $page );
		$I->waitForText( "Workflow $page", 3 );
	}
}
