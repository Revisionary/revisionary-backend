		<?php

			// Additional JS Files
			if (isset($additionalBodyJS) && is_array($additionalBodyJS) ) {

				foreach ($additionalBodyJS as $file) {
					echo '<script src="'.asset_url_nocache('scripts/'.$file).'"></script>';
				}

			}
		?>

	</body>
</html>