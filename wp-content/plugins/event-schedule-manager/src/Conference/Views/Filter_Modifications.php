<?php
/**
 * Handles modifications to outputs.
 *
 * @since   1.0.0
 *
 * @package TEC\Conference\Views
 */

namespace TEC\Conference\Views;

use TEC\Conference\Plugin;

/**
 * Class Filter_Modifications
 *
 * @since   1.0.0
 *
 * @package TEC\Conference\Views
 */
class Filter_Modifications {

	/**
	 * Adds single sessions tags.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function single_session_tags(): void {
		$terms = get_the_terms( get_the_ID(), Plugin::TAGS_TAXONOMY );
		if ( empty( $terms ) ) {
			return;
		}

		if ( is_wp_error( $terms ) ) {
			return;
		}

		$term_names = wp_list_pluck( $terms, 'name' );
		$terms      = implode( ", ", $term_names );
		if ( $terms !== '' && $terms !== '0' ) {
			echo '<li class="wpsc-single-session-taxonomies-taxonomy wpsc-single-session-location"><i class="fas fa-tag"></i>' . $terms . '</li>';
		}
	}

	/**
	 * Filters session speakers output based on speaker display type.
	 *
	 * @since 1.0.0
	 *
	 * @param string $speakers_typed Predefined speakers typed.
	 * @param int    $session_id     Session post ID.
	 *
	 * @return string HTML output of session speakers.
	 */
	public function filter_session_speakers( $speakers_typed, $session_id ): string {
		$speaker_display = get_post_meta( $session_id, 'tec_session_speaker_display', true );

		if ( $speaker_display == 'typed' ) {
			return $speakers_typed;
		}

		$html         = '';
		$speakers_cpt = get_post_meta( $session_id, 'tec_session_speakers', true );

		if ( $speakers_cpt ) {
			ob_start();
			foreach ( $speakers_cpt as $post_id ) {
				$first_name         = get_post_meta( $post_id, 'tec_first_name', true );
				$last_name          = get_post_meta( $post_id, 'tec_last_name', true );
				$full_name          = $first_name . ' ' . $last_name;
				$title_organization = [];
				$title              = ( get_post_meta( $post_id, 'tec_title', true ) )
					? $title_organization[] = get_post_meta( $post_id, 'tec_title', true )
					: null;
				$organization       = ( get_post_meta( $post_id, 'tec_organization', true ) )
					? $title_organization[] = get_post_meta( $post_id, 'tec_organization', true )
					: null;

				?>
				<div class="tec-session-speaker">

					<?php if ( $full_name !== '' && $full_name !== '0' ) { ?>
						<div class="tec-session-speaker-name">
							<?php echo $full_name; ?>
						</div>
					<?php } ?>

					<?php if ( $title_organization ) { ?>
						<div class="tec-session-speaker-title-organization">
							<?php echo implode( ', ', $title_organization ); ?>
						</div>
					<?php } ?>

				</div>
				<?php
			}
			$html .= ob_get_clean();
		}

		return $html;
	}

	/**
	 * Generates session content header based on session tags.
	 *
	 * @since 1.0.0
	 *
	 * @param int $session_id Session post ID.
	 *
	 * @return string HTML output of session content header.
	 */
	public function session_content_header( int $session_id ): string {
		$html         = '';
		$session_tags = get_the_terms( $session_id, Plugin::TAGS_TAXONOMY );
		if ( $session_tags && ! is_wp_error( $session_tags ) ) {
			ob_start();
			?>
			<ul class="tec-session-tags">
				<?php foreach ( $session_tags as $session_tag ) { ?>
					<li class="tec-session-tags-tag">
						<a href="<?php echo get_term_link( $session_tag->term_id, 'tec_session_tag' ); ?>" class="tec-session-tags-tag-link"><?php echo $session_tag->name; ?></a>
					</li>
				<?php } ?>
			</ul>
			<?php
			$html = ob_get_clean();
		}

		return $html;
	}


	/**
	 * Outputs session sponsors.
	 *
	 * @since 1.0.0
	 *
	 * @param int $session_id The session ID.
	 *
	 * @return string The HTML of the session sponsors or empty string.
	 */
	public function session_sponsors( $session_id ): string {
		$session_sponsors = get_post_meta( $session_id, 'tec_session_sponsors', true );
		if ( ! $session_sponsors ) {
			return '';
		}

		$sponsors = [];
		foreach ( $session_sponsors as $sponsor_li ) {
			$sponsors[] = get_the_title( $sponsor_li );
		}

		ob_start();

		if ( $sponsors !== [] ) {
			echo '<div class="tec-session-sponsor"><div class="tec-session-sponsor-label">Presented by: </div>' . implode( ', ', $sponsors ) . '</div>';
		}

		return ob_get_clean();
	}

	/**
	 * Filters single session speakers output based on speaker display type.
	 *
	 * @since 1.0.0
	 *
	 * @param string $speakers_typed Predefined speakers typed.
	 * @param int    $session_id     Session post ID.
	 *
	 * @return string HTML output of single session speakers.
	 */
	public function filter_single_session_speakers( $speakers_typed, $session_id ): string {
		$speaker_display = get_post_meta( $session_id, 'tec_session_speaker_display', true );

		if ( $speaker_display == 'typed' ) {
			return $speakers_typed;
		}

		$html         = '';
		$speakers_cpt = get_post_meta( $session_id, 'tec_session_speakers', true );

		if ( $speakers_cpt ) {
			ob_start();
			?>
			<div class="tec-single-session-speakers__wrap">
				<h2 class="tec-single-session-heading">
					<?php echo esc_html_x( 'Speakers', 'Speakers heading title for single sessions.', 'event-schedule-manager' ); ?>
				</h2>
				<div class="tec-single-session-speakers desktop">
					<?php foreach ( $speakers_cpt as $post_id ) {
						$first_name         = get_post_meta( $post_id, 'tec_first_name', true );
						$last_name          = get_post_meta( $post_id, 'tec_last_name', true );
						$full_name          = $first_name . ' ' . $last_name;
						$title_organization = [];
						$title              = ( get_post_meta( $post_id, 'tec_title', true ) ) ? $title_organization[] = get_post_meta( $post_id, 'tec_title', true ) : null;
						$organization       = ( get_post_meta( $post_id, 'tec_organization', true ) ) ? $title_organization[] = get_post_meta( $post_id, 'tec_organization', true ) : null;

						?>
						<div class="tec-single-session-speakers-speaker">

							<?php if ( has_post_thumbnail( $post_id ) ) {
								echo get_the_post_thumbnail( $post_id, 'thumbnail', [ 'alt' => $full_name, 'class' => 'tec-single-session-speakers-speaker-image' ] );
							} ?>

							<div class="tec-single-session-speakers-speaker-name_wrap">
								<?php if ( $full_name !== '' && $full_name !== '0' ) { ?>
									<h3 class="tec-single-session-speakers-speaker-name">
										<a href="<?php echo esc_url( get_the_permalink( $post_id ) ); ?>"><?php echo $full_name; ?></a>
									</h3>
								<?php } ?>

								<?php if ( $title_organization ) { ?>
									<div class="tec-single-session-speakers-speaker-title-organization">
										<?php echo esc_html( implode( ', ', $title_organization ) ); ?>
									</div>
								<?php } ?>
							</div>
						</div>
						<?php
					}
					?>
				</div>
			</div>
			<?php
			$html .= ob_get_clean();
		}

		return $html;
	}
}
