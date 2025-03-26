// Confirmation of Bank Account Validation Correction Application form

function beneficiary_jb_tagged_form_wp(data){
    // Create a new Date object representing the current date and time
    var currentDate = new Date();

    // Get individual date and time components
    var year = currentDate.getFullYear();
    var month = currentDate.getMonth() + 1; // Months are 0-indexed, so add 1
    var day = currentDate.getDate();
    var hours = currentDate.getHours();
    var minutes = currentDate.getMinutes();
    var seconds = currentDate.getSeconds();

    // Format the date and time as a string
    var formattedDateTime = `${year}_${month}_${day} ${hours}_${minutes}_${seconds}`;

    var mainRepeatedContent = [];
    var mainPageBreakContent = [];
    var loopCount = 0; 
    let benLength = data.length;
    data.forEach(key => {
        loopCount++;
        var beneficiaryID = key.beneficiary_id;
        var mainContent = [
            {
                text: 'Government of West Bengal',
                style: 'header'
            },
            { text: 'Office of the '+key.msg+ ' '+key.block_subdiv_name+' District' +key.district_name, style: 'header' },
            {
                text : 'প্রিয় বিধবাভাতা প্রাপক,',
                margin: [0, 5, 0, 5], font: 'Bangla', style: 'bodyBangla'
            },
            {
                text : [
                    {text: 'বেনিফিশিয়ারি আই ডি', font: 'Bangla', style: 'bodyBangla'},
                    {text: beneficiaryID, bold: true}
                ],
                margin: [0, 5, 0, 5], 
            },
            {
                text : [
                    {text: 'বিধবাভাতা প্রকল্পের উপভোক্তা হিসাবে আপনি যে নথিগুলি জমা করেছেন সেগুলি সহ আপনাকে আগামী ', font: 'Bangla', style: 'bodyBangla'},
                    {text: key.date},
                    {text: 'তারিখে ', font: 'Bangla', style: 'bodyBangla'},
                    {text: key.time},
                    {text: 'টার সময় ', font: 'Bangla', style: 'bodyBangla'},
                    {text: key.block_subdiv_name},
                    {text: 'অফিসে আসতে অনুরোধ করা হচ্ছে। আপনাকে এই নথিগুলি আনতে হবে ঃ\n\n', font: 'Bangla', style: 'bodyBangla'}
                ],
                margin: [0, 5, 0, 5],
            },
            {
                ul: [
                    'আপনার ব্যাংক একাউন্টের পাসবুক যেখানে বিধবাভাতা দেওয়া হচ্ছে,',
                    'পাসবুকের প্রথম পাতার প্রতিলিপি,',
                    'আধার কার্ড,',
                    'আধার কার্ডের একটি প্রতিলিপি,',
                    'স্বামীর মৃত্যুর শংসাপত্র,',
                    'স্বামীর মৃত্যুর শংসাপত্রের একটি প্রতিলিপি।'
                    // { text: 'Item 4', bold: true },
                  ],
                margin: [12, 0, 0, 0], font: 'Bangla', style: 'bodyBangla'
            },
            // {
            //     style: 'tableExample',
            //     table: {
            //         widths: [506],
            //         headerRows: 1,
            //         body: [
            //             [
            //                 {text: 'স্ব ঘোষণা', style: 'tableHeader', alignment: 'center', fillColor: '#eeeeee', font: 'Bangla'}
            //             ],
                        
            //             [{text: '\nআমি,..................................................................................................................................................\n\nস্বামী/পিতার নাম ......................................................................................................................................\n\nব্লক/পৌরসভা: ................................................................................................................................., \n\nজেলা..........................................................................................................................., রাজ্য পশ্চিমবঙ্গ, \n\n(আধার কার্ড নং..................................................................................................................) \nনিশ্চিত করে বলছি যে উপরের তথ্যগুলি সত্য। ভবিষ্যতে যদি এগুলি ভুল প্রমাণিত হয়, আমার অনুমোদিত উপভোক্তাটি বাতিল হয়ে যাবে। সে ক্ষেত্রে আমি যে টাকা সরকারের থেকে পেয়েছিলাম, তা আমি ফেরত দিতে বাধ্য থাকব এবং আমার বিরুদ্ধে আইনি পদক্ষেপ করা যাবে। \n\nতারিখ: ...................................... \n\n\n.................................................................................................................. \nউপভোক্তার সই /টিপছাপ ',font: 'Bangla', fontSize: 13}]
                        
            //         ]
            //     },
            //     margin: [0, 5, 0, 50]
            // },
            {
                canvas: [{
                    type: 'line',
                    x1: 0,
                    y1: 2,
                    x2: 515, // Set to the width of the page
                    y2: 2,
                    lineWidth: 1,
                }, ]
            },
            // {
            //     text: 'লক্ষ্মীর ভান্ডার ব্যাঙ্ক অ্যাকাউন্ট যাচাই-এর রসিদ ',
            //     style: {
            //         alignment: 'center',
            //         fontSize: 13,
            //         decoration: 'underline'
            //     },
            //     margin: [0, 10, 0, 10],
            //     font: 'Bangla'
            // },
            // {
            //     text : 'Smt.............................................................................................................................., \n\nBeneficiary Id ...............................................................................................................',
            //     margin: [0, 5, 0, 5]
            // },
            // {
            //     text : 'এই উপভোক্তার লক্ষ্মীর ভান্ডার ব্যাঙ্ক অ্যাকাউন্ট যাচাই ফর্মটি পেলাম।',
            //     margin: [0, 5, 0, 5],
            //     font: 'Bangla',
            //     style: 'bodyBangla'
            // },
            {
                text: key.msg+' '+key.block_subdiv_name+ ' District '+key.district_name,
                margin: [ 0, 50, 0, 5 ],
                style: {
                    alignment: 'right'
                }
            },
            
        ];
        if (benLength>1 && (loopCount<benLength)) {
            var pageBreakContent = [{ text: '', pageBreak:"after" }];
            mainPageBreakContent = mainContent.concat(pageBreakContent);
        }
        else {
            mainPageBreakContent = mainContent;
        }
        mainRepeatedContent = mainRepeatedContent.concat(mainPageBreakContent);
    });
    // console.log(JSON.stringify(mainRepeatedContent));

    var docDefination = {
        content: mainRepeatedContent,
        styles: {
            header: {
                fontSize: 14,
                bold: false,
                decoration: 'underline',
                margin: [0, 5, 0, 0],
                alignment: 'center'
            },
            headerBangla: {
                fontSize: 14,
                bold: false,
                decoration: 'underline',
                margin: [0, 5, 0, 0],
                alignment: 'center'
            },
            bodyBangla: {
                fontSize: 13,
            },
            tableExample: {
                fontSize: 12,
                margin: [0, 5, 0, 15]
            },
            tableExampleBangla: {
                fontSize: 13,
                margin: [0, 5, 0, 15]
            },
            tableHeader: {
                bold: true,
                fontSize: 12,
                color: 'black'
            }
        },
        defaultStyle: {
            alignment: 'justify',
            font: 'TimesNewRoman',
            fontSize: 12
        },
        pageSize: 'A4',
        pageOrientation: 'portrait', // or 'landscape'
        pageMargins: [40, 40, 40, 40], // [left, top, right, bottom] margins in PDF units (1/72 inch)
    };

    // Generate the PDF
    // pdfMake.createPdf(docDefination).download('Confirmation of bank account validation for Lakshmir Bhandar-' + formattedDateTime + '.pdf');
    
    // For view in another tab
    var win = window.open('', '_blank');
    pdfMake.createPdf(docDefination).open({}, win);
}
