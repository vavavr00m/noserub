<div class="widget widget-event-overview">
	<?php if(!empty($data)): ?>
		<table>
			<thead>
				<tr>
					<th class="discussions"><?php __('Entries'); ?></th>
					<th class="replies"><?php __('Comments'); ?></th>
				</tr>
			</thead>
			<tbody>
				<?php foreach($data as $item): ?>
					<tr>
						<td class="discussions">
							<?echo $html->link(
							    $item['Entry']['title'], 
								'/events/entry/' . Context::eventSlug() . '/' . $item['Entry']['id']
							); ?>
						</td>
						<td class="replies"><?php echo $item['Entry']['comment_count']; ?></td>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
	<?php else: ?>
		<?php __('There is currently no entry for this event') ?>
	<?php endif; ?>
</div>
