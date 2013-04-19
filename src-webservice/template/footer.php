			</div>
			<footer class="site-footer">
			<?php
				$total = count($breadcrumbs);
				foreach ($breadcrumbs as $key => $value)
				{
					echo "<a ";
					foreach ($value as $key2 => $value2)
					{
						echo $key2."=\"".$value2."\" ";
					}
					echo ">".$key."</a>";
					$total--;
					if ($total > 0)
						echo " >> ";
				}
			?>
			</footer>
		</div>
		<script src="js/bajuri.js" type="text/javascript"></script>
		<script src="js/do.js" type="text/javascript"></script>
		<?php foreach ($this->javascripts as $js): ?>

		<script src="js/<?php echo $js ?>.js" type="text/javascript"></script>

		<?php endforeach; ?>
		<!--
		<script src="js/checker.js"></script>
		-->
	</body>
</html>
