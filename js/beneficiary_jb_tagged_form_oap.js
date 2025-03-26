function beneficiary_jb_tagged_form_oap_n(data) {
    // console.log("Received Data:", data);
    var currentDate = new Date();
    var formattedDateTime = currentDate.getFullYear() + "_" + 
                            (currentDate.getMonth() + 1) + "_" + 
                            currentDate.getDate() + " " + 
                            currentDate.getHours() + "_" + 
                            currentDate.getMinutes() + "_" + 
                            currentDate.getSeconds();

    var mainRepeatedContent = [];
    let benLength = data.length;
    let loopCount = 0;

    data.forEach(key => {
        loopCount++;
        var beneficiaryID = key.beneficiary_id ; // Handle undefined IDs
        var mainContent = [
            { text: 'Government of West Bengal', style: 'header' },
            { text: 'Office of the '+key.msg+' '+key.block_subdiv_name+' District' +key.district_name, style: 'header' },
             { text: 'প্রিয় বার্ধক্যভাতা প্রাপক,', margin: [0, 5, 0, 5], font: 'Bangla', style: 'bodyBangla' },
            { 
                text: [
                    { text: 'বেনিফিশিয়ারি আই ডি: ', font: 'Bangla', style: 'bodyBangla' },
                    { text: beneficiaryID, bold: true }
                ],
                margin: [0, 5, 0, 5], 
            },
            {
                text: [
                    { text: 'বার্ধক্যভাতা প্রকল্পের উপভোক্তা হিসাবে আপনি যে নথিগুলি জমা করেছেন সেগুলি সহ আপনাকে আগামী ', font: 'Bangla', style: 'bodyBangla' },
                    { text: key.date },
                    { text: 'তারিখে ', font: 'Bangla', style: 'bodyBangla' },
                    { text: key.time },
                    { text: 'টার সময় ', font: 'Bangla', style: 'bodyBangla' },
                    { text: key.block_subdiv_name },
                    { text: 'অফিসে আসতে অনুরোধ করা হচ্ছে। আপনাকে এই নথিগুলি আনতে হবে ঃ\n\n', font: 'Bangla', style: 'bodyBangla' }
                ],
                margin: [0, 5, 0, 5],
            },
            {
                ul: [
                    'আপনার ব্যাংক একাউন্টের পাসবুক যেখানে বার্ধক্যভাতা টাকা দেওয়া হচ্ছে, ',
                    'পাসবুকের প্রথম পাতার প্রতিলিপি,',
                    'আধার কার্ড,',
                    'আধার কার্ডের একটি প্রতিলিপি,',
                    'এপীক কার্ড বা সচিত্র ভোটার পরিচয়পত্র,',
                    'এপীক কার্ড বা সচিত্র ভোটার পরিচয়পত্রের একটি প্রতিলিপি।'
                ],
                margin: [12, 0, 0, 0], font: 'Bangla', style: 'bodyBangla'
            },
            { 
                canvas: [{
                    type: 'line',
                    x1: 0,
                    y1: 2,
                    x2: 515,
                    y2: 2,
                    lineWidth: 1,
                }]
            },
            {
                text: ''+key.msg+ ' '+key.block_subdiv_name+ ' District '+key.district_name,
                margin: [0, 50, 0, 5],
                style: { alignment: 'right' }
            }
        ];

        if (benLength > 1 && (loopCount < benLength)) {
            mainContent.push({ text: '', pageBreak: "after" });
        }
        
        mainRepeatedContent = mainRepeatedContent.concat(mainContent);
    });

    var docDefinition = {
        content: mainRepeatedContent,
        styles: {
            header: {
                fontSize: 14,
                bold: false,
                decoration: 'underline',
                margin: [0, 5, 0, 0],
                alignment: 'center'
            },
            bodyBangla: { fontSize: 13 },
            tableHeader: { bold: true, fontSize: 12, color: 'black' }
        },
        defaultStyle: {
            alignment: 'justify',
            // font: 'TimesNewRoman',
            fontSize: 12
        },
        pageSize: 'A4',
        pageOrientation: 'portrait',
        pageMargins: [40, 40, 40, 40]
    };

    
    // Generate PDF
    pdfMake.createPdf(docDefinition).download('Bank account validation Date Assign Form for Jai Bangla-' + formattedDateTime + '.pdf');
}
