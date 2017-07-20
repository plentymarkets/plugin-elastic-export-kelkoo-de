
# ElasticExportKelkooDE plugin user guide

<div class="container-toc"></div>

## 1 Registering with Kelkoo

Kelkoo is a price comparison search engine for shopping and travel. The basic service allows users to design and manage their product listings individually. Clicks are paid for with prepaid credit.

## 2 Setting up the data format KelkooDE-Plugin in plentymarkets

The plugin Elastic Export is required to use this format.

Refer to the [Exporting data formats for price search engines](https://knowledge.plentymarkets.com/en/basics/data-exchange/exporting-data#30) page of the manual for further details about the individual format settings.

The following table lists details for settings, format settings and recommended item filters for the format **KelkooDE-Plugin**.
<table>
    <tr>
        <th>
            Settings
        </th>
        <th>
            Explanation
        </th>
    </tr>
    <tr>
        <td class="th" colspan="2">
            Settings
        </td>
    </tr>
    <tr>
        <td>
            Format
        </td>
        <td>
            Choose <b>KelkooDE-Plugin</b>.
        </td>        
    </tr>
    <tr>
        <td>
            Provisioning
        </td>
        <td>
            Choose <b>URL</b>.
        </td>        
    </tr>
    <tr>
        <td>
            File name
        </td>
        <td>
            The file name must have the ending <b>.csv</b> for Kelkoo to be able to import the file successfully.
        </td>        
    </tr>
    <tr>
        <td class="th" colspan="2">
            Item filter
        </td>
    </tr>
    <tr>
        <td>
            Active
        </td>
        <td>
            Choose <b>active</b>.
        </td>        
    </tr>
    <tr>
        <td>
            Markets
        </td>
        <td>
            Choose one or multiple order referrer. The chosen order referrer has to be active at the variation for the item to be exported.
        </td>        
    </tr>
    <tr>
        <td class="th" colspan="2">
            Format settings
        </td>
    </tr>
    <tr>
        <td>
            Order referrer
        </td>
        <td>
        	Choose the order referrer that should be assigned during the order import.
        </td>        
    </tr>
    <tr>
        <td>
            Preview text
        </td>
        <td>
        	This option does not affect this format.
        </td>        
    </tr>
    <tr>
        <td>
            Image
        </td>
        <td>
            Choose <b>First image</b>.
        </td>        
    </tr>
    <tr>
        <td>
            RRP
        </td>
        <td>
            This option is not relevant for this format.
        </td>        
    </tr>
    <tr>
        <td>
            MwSt.-Hinweis
        </td>
        <td>
            This option is not relevant for this format.
        </td>        
    </tr>
    <tr>
        <td>
            Override item availabilty
        </td>
        <td>
            This option is not relevant for this format.
        </td>        
    </tr>
</table>

## 3 Overview of available columns
<table>
    <tr>
        <th>
            Column description
        </th>
        <th>
            Explanation
        </th>
    </tr>
    <tr>
		<td>
			offer-id
		</td>
		<td>
			<b>Required</b><br>
			The <b>SKU</b> of the variation based on the chosen order referrer in the format settings.
		</td>        
	</tr>
	<tr>
		<td>
			title
		</td>
		<td>
			<b>Required</b><br>
			<b>Limitation:</b> max. 80 characters<br>
			According to the format setting <b>item name</b>.
		</td>        
	</tr>
	<tr>
		<td>
			product-url
		</td>
		<td>
			<b>Required</b><br>
			The <b>URL path</b> of the item depending on the chosen <b>client</b> in the format settings.
		</td>        
	</tr>
	<tr>
		<td>
			price
		</td>
		<td>
			<b>Required</b><br>
			 The <b>sales price</b> of the variation.
		</td>        
	</tr>
	<tr>
		<td>
			brand
		</td>
		<td>
			The <b>name of the manufacturer</b> of the item. The <b>external name</b> within <b>Settings » Items » Manufacturer</b> will be preferred if existing.
		</td>        
	</tr>
	<tr>
		<td>
			description
		</td>
		<td>
			<b>Limitation:</b> max. 300 characters<br>
			According to the format setting <b>description</b>.
		</td>        
	</tr>
	<tr>
		<td>
			image-url
		</td>
		<td>
			<b>Limitation:</b> <b>minum size:</b> 300 x 300 pixel. <b>Maximum size:</b> 6.600.000 pixel<br>
			URL of the image according to the format setting <b>image</b>. Variation images are prioritizied over item images.
		</td>        
	</tr>
	<tr>
		<td>
			ean
		</td>
		<td>
			According to the format setting <b>Barcode</b>.
		</td>        
	</tr>
	<tr>
		<td>
			merchant-category
		</td>
		<td>
			The <b>name of the last category level</b> of the <b>category path of the default cateogory</b> for the defined client in the format settings.
		</td>        
	</tr>
	<tr>
		<td>
			availability
		</td>
		<td>
			<b>Required</b><br>
			<b>Allowed values:</b> 1, 4, 5<br>
			Translation according to the format setting <b>Override item availabilty</b>.
		</td>        
	</tr>
	<tr>
		<td>
			delivery-cost
		</td>
		<td>
			<b>Required</b><br>
			According to the format setting <b>shipping costs</b>.
		</td>        
	</tr>
	<tr>
		<td>
			delivery-time
		</td>
		<td>
			The name of the appropriate item availabilty of the variation within <b>Settings » Items » Item availabilty</b>.
		</td>        
	</tr>
	<tr>
		<td>
			ecotax
		</td>
		<td>
			Will be filled automaticly with value 0.
		</td>        
	</tr>
	<tr>
		<td>
			mpn
		</td>
		<td>
			The <b>model</b> within <b>Items » Edit item » Open item » Open variation » Settings » Basic settings</b>.
		</td>        
	</tr>
	<tr>
		<td>
			unit-price
		</td>
		<td>
			The <b>base price information</b> in the format "price / unit". (Example: 10,00 EUR / kilogram)
		</td>        
	</tr>
	<tr>
		<td>
			image-url-(2-4)
		</td>
		<td>
			URL of the image according to the format setting <b>image</b>. Variation images are prioritizied over item images.
		</td>        
	</tr>
</table>

## 4 Licence

This project is licensed under the GNU AFFERO GENERAL PUBLIC LICENSE.- find further information in the [LICENSE.md](https://github.com/plentymarkets/plugin-elastic-export-kelkoo-de/blob/master/LICENSE.md).
