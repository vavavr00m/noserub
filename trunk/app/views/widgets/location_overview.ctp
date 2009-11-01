<div class="widget widget-location-overview">
	<?php if(!empty($data)): ?>
		<table>
			<thead>
				<tr>
					<th class="discussions"><?php __('Entries'); ?></th>
					<th class="replies"><?php __('Comments'); ?></th>
					<th class="activity"><?php __('Activity'); ?></th>
				</tr>
			</thead>
			<tbody>
				<?php foreach($data as $item): ?>
					<tr>
						<td class="discussions">
							<?echo $html->link(
							    $item['Entry']['title'], 
								'/locations/entry/' . Context::locationSlug() . '/' . $item['Entry']['id']
							); ?>
						</td>
						<td class="replies"><?php echo $item['Entry']['comment_count']; ?></td>
						<td class="activity"><?php echo date('Y-m-d H:i:s', strtotime($item['Entry']['last_activity'])); ?></td>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
	<?php else: ?>
		<?php __('There is currently no entry for this location') ?>
	<?php endif; ?>
</div>
