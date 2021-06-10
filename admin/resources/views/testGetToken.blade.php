<table style="width:100%; border: 1px solid black;  border-collapse: collapse;">
    <tr style="border: 1px solid black;  border-collapse: collapse;">
        <td>#</td>
        <td>Name</td>
        <td>Email</td>
    </tr>
    <?php foreach ($details as $detail) { ?>
    <tr>
        <td><?php echo $detail->id; ?></td>
        <td><?php echo $detail->name; ?></td>
        <td><?php echo $detail->email; ?></td>
    </tr>
    <?php } ?>
</table>