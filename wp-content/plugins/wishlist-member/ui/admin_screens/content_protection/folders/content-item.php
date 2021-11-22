<?php $content_lvls = $this->GetContentLevels( '~FOLDER', $item['ID'], true, false ); ?>
<tbody class="outer-tbody content-tbody-<?php echo $item['ID']; ?>">
	<tr class="content-tr content-tr-<?php echo $item['ID']; ?>">
		<td class="text-center">
			<div class="form-check -for-tables">
				<input value="<?php echo $item['ID']; ?>" type="checkbox" class="chk-contentid form-check-input">
				<label class="form-check-label d-none" for=""></label>
			</div>
		</td>
		<td>
			<?php
				$protection_title = array(
					"lock" => "Protected",
					"lock_open" => "Unprotected",
					"security" => "Inherited",
				);
				$protection = $this->FolderProtected( $item['ID'] ) ? "lock" : "lock_open";
				$protection = $protect_inherit ? "security" : $protection;
			?>
			<a href="#" title="<?php echo $protection_title[$protection]; ?>" class="toggle-content-protection" content_type="<?php echo $content_type; ?>" contentids="<?php echo $item['ID']; ?>" content_comment="<?php echo $content_comment ? '1' : '0'; ?>">
				<i class="wlm-icons md-24"><?php echo $protection; ?></i>
			</a>
			<?php echo $item['post_title']; ?>
		</td>
		<td class="text-center">
			<?php
				$protection_status = "";
				if ( $protect_inherit ) {
					$protection_status = $protection_title[$protection];
					if ( $protection == "inherit" ) {
						$protection_status .= " - Protected";
					} else {
						$protection_status .= " - Unprotected";
					}
				}
				if ( !empty( $protection_status ) ) {
					echo "<span title='{$protection_status}'>{$protection_title[$protection]}</span>";
				} else {
					echo $protection_title[$protection];
				}
			?>
		</td>
		<td class="text-center">
			<?php echo $item['writable'] ? 'Yes' : 'No'; ?>
		</td>
		<td class="text-center">
			<?php
				$htaccess = $item['htaccess_exists'] ? "Not Writable" : "Not Found";
				$htaccess = $item['htaccess_writable'] ? "OK" : $item['htaccess_writable'];
			?>
			<?php echo $htaccess; ?>
		</td>
		<td class="text-center">
			<?php
				$file_count = count( glob( $item['full_path'] . '/*' ) ) - count( glob( $item['full_path'] . '/*', GLOB_ONLYDIR ) );
			?>
			<?php if ( $file_count ) : ?>
				<a href="#" data-path="<?php echo $item['full_path']; ?>" class="show-files-btn" title="Show files">
					<?php echo $file_count; ?>
				</a>
			<?php else: echo $file_count ?>
			<?php endif; ?>
		</td>
		<td class="text-center">
			<a href="#" class="toggle-force-download">
				<i class="wlm-icons md-24"><?php echo $item['force_download'] ? 'check' : 'close'; ?></i>
				<input type="hidden" name="content_type" value="<?php echo $content_type; ?>" />
				<input type="hidden" name="contentids" value="<?php echo $item['ID']; ?>" />
				<input type="hidden" name="force_download" value="<?php echo $item['force_download'] ? '1' : '0'; ?>" />
				<input type="hidden" name="action" value="admin_actions" />
				<input type="hidden" name="WishListMemberAction" value="update_content_protection" />
			</a>
		</td>
	</tr>
	<tr>
		<td>&nbsp;</td>
		<td colspan="6">
			<?php
				$levels = "";
				if ( count( $content_lvls ) ) {
					$title = $protection == "lock_open" ? "Unprotected" : "";
					$levels = "<span title='{$title}' style='cursor: default;'>" .implode("</span>, <span title='{$title}' style='cursor: default;'>", $content_lvls ) ."</span>";
				}
				$levels = empty( $levels ) ? "<span class='text-muted'>(No Membership Levels Assigned)</span>" : $levels;
			?>
			<div class="overflow-ellipsis" style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
				&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				<span class="wlm-icons text-muted -o-3" title="Membership Levels" style="cursor: default;">levels_icon</span>
				<?php $protection == "lock_open" ? "text-decoration: line-through;" : ""; ?>
				<span style="vertical-align: middle;" >
					<?php echo $levels; ?>
				</span>
			</div>
		</td>
	</tr>
</tbody>