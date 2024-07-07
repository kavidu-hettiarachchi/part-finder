<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Search Parts
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div style="text-align: right;" class="text-sky-950 text-sm"> Welcome to Part Finder,
                        Now {{ __("You're logged in!") }}</div>

                    <section class="py-8 px-6">
                        <div class="flex flex-wrap -mx-3 items-center">
                            <div class="w-full lg:w-1/2 flex items-center mb-5 lg:mb-0 px-3">
                                <div>
                                    <h2 class="mb-1 text-3xl font-bold" style="font-size: 20px; font-weight: bold;">Part
                                        Finder</h2>
                                    <p class="text-sm text-gray-500 font-medium">This search helps you find part
                                        information. You can search using IE Control Number, Media Number, Part Number,
                                        and Part Name. (Example : 0S1590)</p>
                                    <p style="color: red; text-align: left; margin: 10px 0; font-size: 14px;">
                                        Note: To view more information for each product, click the tree icon arrow.
                                    </p>
                                    <div>
                                        <img src="/img/info.png" style="height: 220px;"/>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </section>


                    <div style="margin: 20px;">
                        <input type="text" id="part-search" style="width: 100%; padding: 10px; margin-bottom: 10px;"
                               placeholder="Search for parts... (IE Control Number, Media Number, Part Number, and Part Name)">
                        <ul id="search-results" style="list-style: none; padding: 0;"></ul>
                        <div id="part-details"></div>
                    </div>

                    <script type="application/javascript">
                        document.getElementById('part-search').addEventListener('keyup', function () {
                            let query = this.value;
                            let results = document.getElementById('search-results');

                            if (query.length > 3) {
                                results.innerHTML = '<li>Loading...</li>';
                                console.log(`Fetching data for query: ${query}`);

                                fetch(`/parts/search?query=${query}`)
                                    .then(response => {
                                        if (!response.ok) {
                                            throw new Error('Network response was not ok');
                                        }
                                        return response.json();
                                    })
                                    .then(data => {
                                        console.log('Data fetched successfully:', data);
                                        results.innerHTML = '';
                                        if (data.length > 0) {
                                            data.forEach(part => {
                                                let li = document.createElement('li');
                                                li.style.border = '1px solid #ccc';
                                                li.style.padding = '10px';
                                                li.style.marginBottom = '5px';
                                                li.style.cursor = 'pointer';
                                                li.innerText = `Control Number ${part.ieControlNumber} - Media Number ${part.mediaNumber}`;
                                                li.addEventListener('click', () => {
                                                    displayPartDetails(part);
                                                });
                                                results.appendChild(li);
                                            });
                                        } else {
                                            results.innerHTML = '<li>No results found</li>';
                                        }
                                    })
                                    .catch(error => {
                                        results.innerHTML = '<li>Error loading suggestions</li>';
                                        console.error('Error fetching data:', error);
                                    });
                            } else {
                                results.innerHTML = '';
                                document.getElementById('part-details').innerHTML = '';
                            }
                        });

                        function displayPartDetails(part) {
                            let details = document.getElementById('part-details');
                            details.innerHTML = `
        <h2>Part Details</h2>
        <div id="part-tree"></div>
    `;

                            // Create a tree structure from the part data
                            let treeData = [{
                                text: `IE Control Number: ${part.ieControlNumber || 'N/A'}`,
                                children: [
                                    {text: `Media Number: ${part.mediaNumber || 'N/A'}`},
                                    {text: `Publication Date: ${part.iePublicationDate || 'N/A'}`},
                                    {text: `Update Date: ${part.ieUpdateDate || 'N/A'}`},
                                    {text: `Is Expanded Mining Product: ${part.isExpandedMiningProduct === 0 ? 'No' : 'Yes'}`},
                                    {
                                        text: 'Group Parts',
                                        children: part.group_parts && part.group_parts.length > 0 ? part.group_parts.map(groupPart => ({
                                            text: `Part Number: ${groupPart.partNumber || 'N/A'}`,
                                            children: [
                                                {text: `Org Code: ${groupPart.orgCode || 'N/A'}`},
                                                {text: `Part Name: ${groupPart.partName || 'N/A'}`},
                                                {text: `Part Language: ${groupPart.partLanguage || 'N/A'}`},
                                                {text: `Modifier: ${groupPart.modifier || 'N/A'}`},
                                                {text: `Modifier Language: ${groupPart.modifierLanguage || 'N/A'}`},
                                                {text: `Serviceability Indicator: ${groupPart.serviceabilityIndicator === 0 ? 'No' : 'Yes'}`},
                                                {text: `Alternate Part Type: ${groupPart.alternatePartType || 'N/A'}`},
                                                {text: `Alternate: ${groupPart.hasAlternate === 0 ? 'No' : 'Yes'}`},
                                                {text: `CCR Part: ${groupPart.isCCRPart === 0 ? 'No' : 'Yes'}`}
                                            ]
                                        })) : [{text: 'No group parts available.'}]
                                    },
                                    {
                                        text: 'Line Items',
                                        children: part.line_items && part.line_items.length > 0 ? part.line_items.map(lineItem => ({
                                            text: `Part Number: ${lineItem.partNumber || 'N/A'}`,
                                            children: [
                                                {text: `Part Name: ${lineItem.partName  || 'N/A'}`},
                                                {text: `Part Language: ${lineItem.partNameLanguage  || 'N/A'}`},
                                                {text: `Note Codes: ${lineItem.noteCodes  || 'N/A'}`},
                                                {text: `Org Code: ${lineItem.orgCode  || 'N/A'}`},
                                                {text: `Quantity: ${lineItem.quantity  || 'N/A'}`},
                                                {text: `Part Sequence Number: ${lineItem.partSequenceNumber  || 'N/A'}`},
                                                {text: `Parentage: ${lineItem.parentage  || 'N/A'}`},
                                                {text: `System Control Number: ${lineItem.ieSystemControlNumber  || 'N/A'}`},
                                                {text: `Disambiguation: ${lineItem.disambiguation  || 'N/A'}`},
                                                {text: `MediaNumber: ${lineItem.mediaNumber  || 'N/A'}`},
                                                {text: `Component Id: ${lineItem.componentId  || 'N/A'}`},
                                                {text: `Comments: ${lineItem.comments  || 'N/A'}`},
                                                {text: `Reference Number: ${lineItem.referenceNumber  || 'N/A'}`},
                                                {text: `Alternate Part Type: ${lineItem.alternatePartType  || 'N/A'}`},
                                                {text: `Modifier: ${lineItem.modifier  || 'N/A'}`},
                                                {text: `Modifier Language: ${lineItem.isCCRPart  || 'N/A'}`},
                                                {text: `CCR Part: ${lineItem.modifierLanguage  === 0 ? 'No' : 'Yes'}`},
                                                {text: `Alternate: ${lineItem.hasAlternate  === 0 ? 'No' : 'Yes'}`},
                                                {text: `Serviceability Indicator: ${lineItem.serviceabilityIndicator === 0 ? 'No' : 'Yes'}`},
                                                {
                                                    text: 'Images',
                                                    children: lineItem.image_identifiers && lineItem.image_identifiers.length > 0 ? lineItem.image_identifiers.map(image => ({
                                                        text: `Image ID: ${image.imageId}`
                                                    })) : [{text: 'No images available.'}]
                                                }
                                            ]
                                        })) : [{text: 'No line items available.'}]
                                    },
                                    {
                                        text: 'Captions',
                                        children: part.captions && part.captions.length > 0 ? part.captions.map(caption => ({
                                            text: `Caption: ${caption.name || 'N/A'}`,
                                            children: [
                                                {text: `Language: ${caption.language || 'N/A'}`},
                                                {text: `Org Code: ${caption.orgCode || 'N/A'}`},
                                                {
                                                    text: 'Referenced Caption Parts',
                                                    children: caption.referenced_caption_parts && caption.referenced_caption_parts.length > 0 ? caption.referenced_caption_parts.map(refPart => ({
                                                        text: `Part Number: ${refPart.partNumber || 'N/A'}`,
                                                        children: [
                                                            {text: `Org Code: ${refPart.orgCode || 'N/A'}`},
                                                            {text: `System Control Number: ${refPart.ieSystemControlNumber || 'N/A'}`},
                                                            {text: `Disambiguation: ${refPart.disambiguation === 0 ? 'No' : 'Yes'}`}
                                                        ]
                                                    })) : [{text: 'No referenced caption parts available.'}]
                                                }
                                            ]
                                        })) : [{text: 'No captions available.'}]
                                    },
                                    {
                                        text: 'SMCS Codes',
                                        children: part.smcs_codes && part.smcs_codes.length > 0 ? part.smcs_codes.map(code => ({
                                            text: `Code: ${code.code || 'N/A'}`,
                                            children: [
                                                {text: `Description: ${code.description || 'N/A'}`},
                                                {text: `Language: ${code.language || 'N/A'}`}
                                            ]
                                        })) : [{text: 'No SMCS codes available.'}]
                                    },
                                    {
                                        text: 'Notes',
                                        children: part.notes && part.notes.length > 0 ? part.notes.map(note => ({
                                            text: `Note Code: ${note.noteCode || 'N/A'}`,
                                            children: [
                                                {text: `Note Name: ${note.noteName || 'N/A'}`},
                                                {text: `Language: ${note.language || 'N/A'}`}
                                            ]
                                        })) : [{text: 'No notes available.'}]
                                    }
                                ]
                            }];

                            // Log the tree data for debugging
                            console.log('Tree data:', treeData);

                            // Render the tree view
                            $('#part-tree').jstree({
                                'core': {
                                    'data': treeData
                                }
                            });

                            // Update the data view div
                            let dataView = document.querySelector('.data-view');
                            dataView.innerHTML = details.innerHTML;
                        }

                    </script>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
