<?php
	$content_lvls = $this->GetContentLevels( "categories", $item['ID'], true, false, $immutable );

	$protection_title = array(
		"lock" => "Protected",
		"lock_open" => "Unprotected",
		"inherit" => "Inherited",
		"inherit_unprotected" => "Inherited",
	);

	$protect_inherit = $this->SpecialContentLevel( $item['ID'], 'Inherit', null, '~CATEGORY' );
	$protection = $this->CatProtected( $item['ID'] ) ? "lock" : "lock_open";
	if ( $protect_inherit ) {
		if ( $protection == "lock" ) $protection = "inherit";
		else $protection = "inherit_unprotected";
	}
	$child_class = $protection == 'inherit' || $protection == 'inherit_unprotected' ? "child-tbody-{$item['parent']}" : "";
?>
<tbody class="outer-tbody content-tbody-<?php echo $item['ID']; ?> <?php echo $child_class; ?>">
	<tr class="content-tr content-tr-<?php echo $item['ID']; ?>">
		<td class="text-center">
			<div class="form-check -for-tables">
				<input value="<?php echo $item['ID']; ?>" type="checkbox" class="chk-contentid form-check-input">
				<label for="" class="form-check-label d-none"></label>
			</div>
		</td>
		<td>
			<a href="#" title="<?php echo $protection_title[$protection]; ?>" parent="<?php echo $item['parent']; ?>" class="toggle-content-protection" content_type="<?php echo $content_type; ?>" contentids="<?php echo $item['ID']; ?>" content_comment="<?php echo $content_comment ? '1' : '0'; ?>">
				<i class="wlm-icons md-24"><?php echo $protection; ?></i>
			</a>
			<?php echo str_repeat("&mdash;", $item['deep']) ." " .$item['name']; ?>
		</td>
		<td>
			<?php
				$protection_status = "";
				if ( $protect_inherit ) {
					$protection_status = $protection_title[$protection];
					if ( $protection == "inherit" ) {
						$protection_status .= " - Protected";
						$prot_cat = array();
						if ( $item['parent'] ) {
							$pcat = get_category( $item['parent'] );
							if ( $pcat ) $prot_cat[] = $pcat->name;
						}
						$prot_cat = count( $prot_cat ) > 0 ? " from " .implode(", ", $prot_cat ) : "";
						$protection_status .= $prot_cat;
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
		<td class="text-center"><?php echo $item['taxonomy']; ?></td>
	</tr>
	<tr>
		<td>&nbsp;</td>
		<td colspan="3">
			<?php
				$wpm_levels = $this->GetOption('wpm_levels');
				$content_mylvls = array();
				foreach ( $content_lvls as $key => $value ) {
					if ( !isset($wpm_levels[$key]["allcategories"]) || empty($wpm_levels[$key]["allcategories"]) ) {
						$content_mylvls[$key] = trim($value);
						unset($content_lvls[$key]);
					}
				}
				$levels = "";
				if ( count( $content_lvls ) ) {
					$title = "This level has access to all {$content_type}";
					$levels = "<em class='text-muted' title='{$title}' style='cursor: default;'>" .implode("</em>, <em class='text-muted' title='{$title}' style='cursor: default;'>", $content_lvls ) ."</em>";
				}
				if ( count( $content_mylvls ) ) {
					$levels  = !empty( $levels ) ? $levels .", " : $levels;
					if ( $protection == "lock_open" ) {
						$levels .= "<span>" .implode("</span>, <span>", $content_mylvls ) ."</span>";
					} else {
						$levels .= implode(", ", $content_mylvls );
					}
				}
				$levels = empty( $levels ) ? "<span class='text-muted'>(No Membership Levels Assigned)</span>" : $levels;
			?>
			<div class="overflow-ellipsis" style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
				&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				<span class="wlm-icons text-muted -o-3" title="Membership Levels" style="cursor: default;">levels_icon</span>
				<?php $protection == "lock_open" || $protection == "inherit_unprotected" ? "text-decoration: line-through;" : ""; ?>
				<span style="vertical-align: middle;" >
					<?php echo $levels; ?>
				</span>
			</div>
		</td>
	</tr>
</tbody>