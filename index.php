<?php
/*
Plugin Name: and_csv_plugin
Description: Плагин создания постов блога.
Author: Andrii Aliabiev
*/
add_filter('the_content', 'and_function2');
function and_function2($content)
{
?>

<form method="POST" action="index.php" enctype="multipart/form-data"> 
<input type="hidden" name= "MAX_FILE_SIZE" value="3000000">
<input type="file" name="csv"><br>
<button type="submit" class="btn btn-primary">Send</button>
</form>

<?php
global $wpdb;
if( isset($_FILES['csv']) )
{          
	if (mime_content_type($_FILES['csv']['tmp_name']) == "text/csv" || mime_content_type($_FILES['csv']['tmp_name']) == "text/plain")
	{
	    $row = 1;
		if (($handle = fopen($_FILES['csv']['tmp_name'], "r")) !== FALSE)
		{
			fgetcsv($handle, 1000, ","); //первая строка в csv файле - название столбцов, не добавляется в БД
		    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) 
		    {
			    $num = count($data);
			    $row++;
			    for ($c=0; $c < $num; $c++) 
			    {
				    $elem = explode(";", $data[$c]);
	                $table_name = $wpdb->prefix . "posts";
	                $wpdb->insert($table_name, array(
	                	      "post_name" => $elem[0], 
	                		  "post_content" => $elem[1], 
	                		  "post_title" => $elem[2], 
	                		  "post_date" => $elem[3]), 
	                	array("%s", "%s", "%s", "%s"));

	                //привязка к категориям:   
                    $table_name2 = $wpdb->prefix . "term_relationships";
	                $wpdb->insert($table_name2, array(
	                	      "object_id" => $post['id'], 
	                		  "term_taxonomy_id" => rand(1,3)), 
	                   	array("%d", "%d"));
		        }
			}
		    fclose($handle);
		}
    }   
        
    else 
    	echo "Выберите файл с раширением .csv";

}



}

?>