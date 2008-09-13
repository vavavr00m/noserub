<p>
	Application <strong><?php echo $applicationName; ?></strong> asked for 
	permission to access your data
</p>
<form method="post" action="/pages/oauth/authorize">
	<input type="submit" value="allow" name="allow" />
	<input type="submit" value="deny" name="deny" />
</form>