<section id="prog-profiler">
    <header>
        <nav>
            <ul>
                <li class="logo">Creo Profiler</li>
                <?php foreach (array_keys($data) as $section) : ?>
                <a href="#" id="prog-section-<?= $section ?>" data-table="<?= $section ?>">
                    <li class="nav">
                        <?= ucwords(str_replace(['-', '_'], ' ', $section)) ?>
                    </li>
                </a>
                <?php endforeach ?>
                <a href="#" class="prog-close-profiler">
                    <li class="nav" style="display: none">X</li>
                </a>
                <a href="#" class="prog-hide-profiler" title="Show/Hide Profiler">
                    <li>&#8596;</li>
                </a>
            </ul>
        </nav>
    </header>
    <div class="prog-table-data">
        <?php foreach ($data as $section => $values) : ?>
            <?php switch ($section) : ?>
<?php case 'queries': ?>
        <table id="prog-<?= $section ?>-table" border="0" cellpadding="0" cellspacing="0">
            <tr>
                <th class="query">Query</th>
                <th class="query-time">Time Taken</th>
                <th class="query-memory">Memory Usage</th>
                <th class="query-params">Parameters</th>
            </tr>
            <?php foreach ($values as $query) : ?>
            <tr>
                <td><?= $query['sql'] ?></td>
                <td><?= $query['time_taken'] ?></td>
                <td><?= $query['memory_usage'] ?></td>
                <td>
                <?php foreach ($query['params'] as $params) : ?>
                    <?php foreach ($params as $type => $param) : ?>
                        <?php if ($param instanceof \DateTime) : ?>
                    (<span style="color: #d83f3f"><?= $type ?></span>) <?= $param->format('Y-m-d H:i:s') ?><br>
                        <?php else : ?>
                    (<span style="color: #d83f3f"><?= $type ?></span>) <?= (is_bool($param)) ? ($param === true) ? 'true' : 'false' : $param ?><br>
                        <?php endif ?>
                    <?php endforeach ?>
                <?php endforeach ?>
                </td>
            </tr>
            <?php endforeach ?>
        </table>
<?php break ?>
<?php case 'timers': ?>
        <table id="prog-<?= $section ?>-table" border="0" cellpadding="0" cellspacing="0">
            <tr>
                <th class="timer">Timer</th>
                <th class="timer-time">Time Taken</th>
            </tr>
            <?php foreach ($values as $timer => $timerValues) : ?>
            <tr>
                <td><?= $timer ?></td>
                <td>
                    <?php
                    $style = '';
                    if ($timerValues['time_seconds'] > 0.3) { $style = 'color:orange;font-weight:bold'; }
                    if ($timerValues['time_seconds'] > 0.7) { $style = 'color:red;font-weight:bold'; }
                    ?>
                    <span style="<?=$style?>;"><?= $timerValues['time_seconds'] . ' seconds' ?></span>
                </td>
            </tr>
            <?php endforeach ?>
        </table>
<?php break ?>
<?php case 'memory_measurements': ?>
        <table id="prog-<?= $section ?>-table" border="0" cellpadding="0" cellspacing="0">
            <tr>
                <th class="memory">Memory Measurement</th>
                <th class="memory-usage">Usage</th>
            </tr>
            <?php foreach ($values as $measurement => $measurementValues) : ?>
            <tr>
                <td><?= $measurement ?></td>
                <td><?= $measurementValues['usage_kb'] . ' kB' ?></td>
            </tr>
            <?php endforeach ?>
        </table>
<?php break ?>
<?php case 'included_files': ?>
        <table id="prog-<?= $section ?>-table" border="0" cellpadding="0" cellspacing="0">
            <tr>
                <th class="files">File</th>
                <th class="files-size">Size</th>
            </tr>
            <?php foreach ($values as $file => $size) : ?>
            <tr>
                <td><?= $file ?></td>
                <td><?= $size . ' kB' ?></td>
            </tr>
            <?php endforeach ?>
        </table>
<?php break ?>
<?php case 'globals': ?>
        <table id="prog-<?= $section ?>-table" border="0" cellpadding="0" cellspacing="0">
            <tr>
                <th class="globals">Global</th>
                <th class="globals-key">Key</th>
                <th class="globals-value">Value</th>
            </tr>
            <?php foreach ($values as $global => $keyVal) : ?>
            <?php foreach ($keyVal as $key => $val) : ?>
            <tr>
                <td><?= strtoupper($global) ?></td>
                <td><?= $key ?></td>
                <td>
                    <?php if (is_array($val)) : ?>
                        <?php foreach ($val as $value) : ?>
                            <?= $value ?>
                        <?php endforeach ?>
                    <?php else : ?>
                        <?= $val ?>
                    <?php endif ?>
                </td>
            </tr>
            <?php endforeach ?>
            <?php endforeach ?>
        </table>
            <?php endswitch ?>
        <?php endforeach ?>
    </div>
</section>

<?php if ($jquery === true) : ?>
<?php // Add jQuery if it's not already available from the parent app ?>
<script>window.jQuery || document.write('<script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"><\/script>')</script>
<?php endif ?>
<script>
<?php include __DIR__ . '/profiler.js' ?>
</script>
