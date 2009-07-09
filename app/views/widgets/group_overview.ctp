<div class="widget widget-group-overview">
	<?php if(!empty($data)): ?>
		<table>
			<thead>
				<tr>
					<th class="discussions"><?php __('Discussions'); ?></th>
					<th class="replies"><?php __('Replies'); ?></th>
					<th class="activity"><?php __('Activity'); ?></th>
				</tr>
			</thead>
			<tbody>
				<?php foreach($data as $item): ?>
					<tr>
						<td class="discussions">
							<?= $html->link(
								$item['Entry']['title'], 
								'/groups/entry/' . Context::groupSlug() . '/' . $item['Entry']['id']
							); ?>
						</td>
						<td class="replies"><?= $item['Entry']['comment_count']; ?></td>
						<td class="activity"><?= $item['Entry']['published_on']; ?></td>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
	<?php else: ?>
		<?php __('There is currently no entry in this group.') ?>
	<?php endif; ?>
</div>
