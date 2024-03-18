async function generateCoursePaymentPdf(course_payments) {
    try {
        const contents = [];
        for (const payment of course_payments) {
            const study_detail = payment.study_detail;
            const student = study_detail.student;
            const course_detail = study_detail.course_detail;
            const { course, session, course_days } = course_detail;

            let total;
            const payFromDate = moment(payment.from_date, DATE_FORMAT);
            const payToDate = moment(payment.to_date, DATE_FORMAT);

            if (payment.dis_percent == 100) {
                total = (0).toFixed(2);
            } else {
                if (course_detail.pay_type === 'FULL') {
                    const courseStartDate = moment(course_detail.start_date, DATE_FORMAT);
                    const courseEndDate = moment(course_detail.end_date, DATE_FORMAT)
                    const course_days = courseEndDate.add(1, 'day').diff(courseStartDate, 'day');

                    const fee_per_day = payment.course_fee.course_fee / course_days;

                    const pay_days = payToDate.add(1, 'day').diff(payFromDate, 'day');

                    const fee_to_pay = (fee_per_day === pay_days ? payment.course_fee.course_fee : (fee_per_day * pay_days)) * 1000;
                    total = (fee_to_pay - ((fee_to_pay * payment.dis_percent) / 100)) / 1000;
                } else {
                    const pay_months = payToDate.diff(payFromDate, 'month');
                    total = (((payment.course_fee.course_fee * 1000) - (((payment.course_fee.course_fee * 1000) * payment.dis_percent) / 100)) * pay_months) / 1000;
                }
                const fixed = (total).toFixed(2);
                total = (total - fixed) > 0 ? (Number(fixed) + 0.01).toFixed(2) : fixed;
            }


            const block = {
                stack: [
                    {
                        image: 'bigLogo',
                        width: 200,
                        alignment: 'center',
                        margin: [0, 0, 0, 10],
                    },
                    {
                        text: [
                            'Receipt №: ',
                            {
                                text: payment.code_label,
                                color: 'red'
                            }
                        ],
                        font: 'Roboto',
                        fontSize: 9,
                        color: 'blue',
                        bold: true,
                        alignment: 'left',
                        margin: [10, 0, 0, 2.5],
                    },
                    {
                        text: [
                            'DATE: ',
                            {
                                text: payment.created_at,
                                color: 'red'
                            }
                        ],
                        font: 'Roboto',
                        fontSize: 9,
                        color: 'blue',
                        bold: true,
                        alignment: 'left',
                        margin: [10, 0, 0, 0],
                    },
                    {
                        text: 'Receipt',
                        font: 'Roboto',
                        fontSize: 11,
                        bold: true,
                        color: 'red',
                        decoration: 'underline',
                        decorationStyle: 'solid',
                        decorationColor: 'red',
                        alignment: 'center',
                        margin: [0, 0, 0, 5],
                    },
                    {
                        columns: [
                            {
                                width: '75%',
                                text: [
                                    'Name: ',
                                    {
                                        text: student.name_en,
                                        color: 'black'
                                    },
                                ],

                            },
                            {
                                width: '25%',
                                text: [
                                    'Sex: ',
                                    {
                                        text: student.gender.en_full,
                                        color: 'black'
                                    },
                                ],
                            },
                        ],
                    },
                    {
                        columns: [
                            {
                                width: '50%',
                                text: [
                                    'Course: ',
                                    {
                                        text: course.name,
                                        color: 'black'
                                    },
                                ],

                            },
                            {
                                width: '25%',
                                text: [
                                    'Subject: ',
                                    {
                                        text: course.course_type.en,
                                        color: 'black'
                                    },
                                ],
                            },
                            {
                                width: '25%',
                                text: [
                                    'Level: ',
                                    {
                                        text: course.level,
                                        color: 'black'
                                    },
                                ],
                            }
                        ],
                    },
                    {
                        columns: [
                            {
                                width: '*',
                                text: [
                                    'Days: ',
                                    {
                                        text: course_days.map(obj => obj.short_name).toString(),
                                        color: 'black'
                                    },
                                ],

                            },
                            {
                                width: 'auto',
                                text: [
                                    'Session: ',
                                    // {
                                    //     text: session.shift.en,
                                    //     color: 'black'
                                    // },
                                    {
                                        text: `${session.start_time}-${session.end_time}(${session.shift.en}) `,
                                        color: 'black'
                                    },
                                ],
                                margin: [0, 0, 5, 0]
                            },
                            {
                                width: '27.5%',
                                text: [
                                    'School Fee: ',
                                    {
                                        text: payment.course_fee.course_fee + '.25$' + (course_detail.pay_type === 'FULL' ? '/FULL' : '/Month'),
                                        color: 'black'
                                    },
                                ],
                            }
                        ],
                    },
                    {
                        columns: [
                            {
                                width: '*',
                                text: [
                                    'From: ',
                                    {
                                        text: payment.from_date,
                                        color: 'black'
                                    },
                                ],

                            },
                            {
                                width: '*',
                                text: [
                                    'To: ',
                                    {
                                        text: payment.to_date,
                                        color: 'black'
                                    },
                                ],
                            },
                            {
                                width: '27.5%',
                                text: [
                                    'Discount: ',
                                    {
                                        text: payment.dis_percent + '%',
                                        color: 'black'
                                    },
                                ],
                            }
                        ],
                    },
                    {
                        columns: [
                            {
                                width: '*',
                                text: [
                                    {
                                        text: 'Note:',
                                        color: 'red',
                                        decoration: 'underline',
                                        decorationStyle: 'solid',
                                        decorationColor: 'red',
                                    },
                                    {
                                        text: payment.description !== null ? "\t" + payment.description : '',
                                        color: 'black',
                                    },
                                ],

                            },
                            {
                                width: '27.5%',
                                text: [
                                    'Total: ',
                                    {
                                        text: total + '$',
                                        color: 'black'
                                    },

                                ],
                            }
                        ],
                        margin: [0, 0, 0, 20],
                    },
                    {
                        columns: [
                            {
                                width: '*',
                                text: 'Approver'
                            },
                            {
                                width: '*',
                                text: 'Receiver'
                            }
                        ],
                        font: 'Roboto',
                        fontSize: 10,
                        color: 'blue',
                        bold: true,
                        decoration: 'underline',
                        decorationStyle: 'solid',
                        decorationColor: 'blue',
                        alignment: 'center',
                        margin: [0, 0, 0, 45],
                    },
                    {
                        text: 'ប្រាក់ដែលបង់ហេីយមិនអាចដកវិញបានទេ! សូមយកវិក័ត្របត្រពេលបង់ប្រាក់រួច។',
                        font: 'KhmerOSMoul',
                        fontSize: 9,
                        color: 'red',
                        decoration: 'underline',
                        decorationStyle: 'solid',
                        decorationColor: 'red',
                        alignment: 'center',
                    },
                ],
                style: {
                    font: 'KhmerOSSystem',
                    fontSize: 9,
                    color: 'blue'
                },
                width: '50%',
                margin: [15, 20, 15, 5]
            };

            contents.push(
                {
                    columns: [
                        block,
                        {
                            canvas: [
                                {
                                    type: 'line',
                                    x1: 0,
                                    y1: 0,
                                    x2: 0,
                                    y2: 595 / 2,
                                    lineWidth: 0.5
                                },
                            ]
                        },
                        block
                    ],
                },
                {
                    canvas: [
                        {
                            type: 'line',
                            x1: 0,
                            y1: 0,
                            x2: 842,
                            y2: 0,
                            lineWidth: 0.5
                        },
                    ]
                },
                {
                    columns: [
                        block,
                        {
                            canvas: [
                                {
                                    type: 'line',
                                    x1: 0,
                                    y1: 0,
                                    x2: 0,
                                    y2: 595 / 2,
                                    lineWidth: 0.5
                                },
                            ]
                        },
                        block
                    ],
                    pageBreak: payment.id_course_payment !== course_payments[course_payments.length - 1].id_course_payment ? 'after' : ''
                },
            )
        }
        const pdfDocGenerator = pdfMake.createPdf({
            pageSize: 'A4',
            pageOrientation: 'landscape',
            images: LOGOS,
            pageMargins: [0, 0, 0, 0],
            content: JSON.parse(JSON.stringify(contents)),

        }, null, PDF_FONTS);
        return new Promise((resolve, reject) => {
            pdfDocGenerator.getDataUrl((dataUrl) => {
                const iframe = document.querySelector("iframe");
                iframe.src = dataUrl;
                $('#PdfModal').modal('show');
                resolve(); // Resolve the Promise when the code is finished
            });
        });
    } catch (error) {
        throw error
    }
}
async function generateTransportPaymentPdf(transport_payments) {
    try {
        const contents = [];
        for (const payment of transport_payments) {
            const { transport_detail, transport_fee } = payment;
            const student = transport_detail.student;
            const { region, transport_in, transport_out } = transport_detail;

            let total;
            const payFromDate = moment(payment.from_date, DATE_FORMAT);
            const payToDate = moment(payment.to_date, DATE_FORMAT);
            const duration = payToDate.diff(payFromDate, 'month');
            const fee_to_pay = (transport_fee.monthly_fee * duration) * 1000;
            total = (fee_to_pay - (fee_to_pay * payment.dis_percent / 100)) / 1000;
            const fixed = (total).toFixed(2);
            total = (total - fixed) > 0 ? (Number(fixed) + 0.01).toFixed(2) : fixed;
            const block = {
                stack: [
                    {
                        image: 'bigLogo',
                        width: 200,
                        alignment: 'center',
                        margin: [0, 0, 0, 10],
                    },
                    {
                        text: [
                            'Receipt №: ',
                            {
                                text: payment.code_label,
                                color: 'red'
                            }
                        ],
                        font: 'Roboto',
                        fontSize: 9,
                        color: 'green',
                        bold: true,
                        alignment: 'left',
                        margin: [10, 0, 0, 2.5],
                    },
                    {
                        text: [
                            'DATE: ',
                            {
                                text: payment.created_at,
                                color: 'red'
                            }
                        ],
                        font: 'Roboto',
                        fontSize: 9,
                        color: 'green',
                        bold: true,
                        alignment: 'left',
                        margin: [10, 0, 0, 0],
                    },
                    {
                        text: 'Receipt',
                        font: 'Roboto',
                        fontSize: 11,
                        bold: true,
                        color: 'red',
                        decoration: 'underline',
                        decorationStyle: 'solid',
                        decorationColor: 'red',
                        alignment: 'center',
                        margin: [0, 0, 0, 5],
                    },
                    {
                        columns: [
                            {
                                width: '75%',
                                text: [
                                    'Name: ',
                                    {
                                        text: student.name_en,
                                        color: 'black'
                                    },
                                ],

                            },
                            {
                                width: '25%',
                                text: [
                                    'Sex: ',
                                    {
                                        text: student.gender.en_full,
                                        color: 'black'
                                    },
                                ],
                            },
                        ],
                    },
                    {
                        columns: [
                            {
                                width: '40%',
                                text: [
                                    'Region: ',
                                    {
                                        text: region.name_en,
                                        color: 'black'
                                    },
                                ],

                            },
                            {
                                width: '35%',
                                text: [
                                    'Session: ',
                                    {
                                        text: transport_detail.in_time + '-' + transport_detail.out_time,
                                        color: 'black'
                                    },
                                ],
                            },
                            {
                                width: '25%',
                                text: [
                                    'Fee: ',
                                    {
                                        text: transport_fee.monthly_fee + '$ / Month',
                                        color: 'black'
                                    },
                                ],
                            },
                        ],
                    },
                    {
                        columns: [
                            {
                                width: '*',
                                text: [
                                    'Vehicle: ',
                                    {
                                        text: `${transport_in.vehicle.name}`,
                                        color: 'black'
                                    },
                                ],

                            },
                            {
                                width: '*',
                                text: [
                                    'From: ',
                                    {
                                        text: payment.from_date,
                                        color: 'black'
                                    },
                                ],

                            },
                            {
                                width: '*',
                                text: [
                                    'To: ',
                                    {
                                        text: payment.to_date,
                                        color: 'black'
                                    },
                                ],
                            },
                            {
                                width: '25%',
                                text: [
                                    'Discount: ',
                                    {
                                        text: payment.dis_percent + '%',
                                        color: 'black'
                                    },
                                ],
                            }
                        ],
                    },
                    {
                        columns: [
                            {
                                width: '*',
                                text: [
                                    {
                                        text: 'Note:',
                                        color: 'red',
                                        decoration: 'underline',
                                        decorationStyle: 'solid',
                                        decorationColor: 'red',
                                    },
                                    {
                                        text: payment.description !== null ? "\t" + payment.description : '',
                                        color: 'black',
                                    },
                                ],

                            },
                            {
                                width: '25%',
                                text: [
                                    'Total: ',
                                    {
                                        text: total + '$',
                                        color: 'black'
                                    },

                                ],
                            }
                        ],
                        margin: [0, 0, 0, 25],
                    },
                    {
                        columns: [
                            {
                                width: '*',
                                text: 'Approver'
                            },
                            {
                                width: '*',
                                text: 'Receiver'
                            }
                        ],
                        font: 'Roboto',
                        fontSize: 10,
                        color: 'green',
                        bold: true,
                        decoration: 'underline',
                        decorationStyle: 'solid',
                        decorationColor: 'green',
                        alignment: 'center',
                        margin: [0, 0, 0, 55],
                    },
                    {
                        text: 'ប្រាក់ដែលបង់ហេីយមិនអាចដកវិញបានទេ! សូមយកវិក័ត្របត្រពេលបង់ប្រាក់រួច។',
                        font: 'KhmerOSMoul',
                        fontSize: 9,
                        color: 'red',
                        decoration: 'underline',
                        decorationStyle: 'solid',
                        decorationColor: 'red',
                        alignment: 'center',
                    },
                ],
                style: {
                    font: 'KhmerOSSystem',
                    fontSize: 9,
                    color: 'green'
                },
                width: '50%',
                margin: [15, 20, 15, 5]
            };

            contents.push(
                {
                    columns: [
                        block,
                        {
                            canvas: [
                                {
                                    type: 'line',
                                    x1: 0,
                                    y1: 0,
                                    x2: 0,
                                    y2: 595 / 2,
                                    lineWidth: 0.5
                                },
                            ]
                        },
                        block
                    ],
                },
                {
                    canvas: [
                        {
                            type: 'line',
                            x1: 0,
                            y1: 0,
                            x2: 842,
                            y2: 0,
                            lineWidth: 0.5
                        },
                    ]
                },
                {
                    columns: [
                        block,
                        {
                            canvas: [
                                {
                                    type: 'line',
                                    x1: 0,
                                    y1: 0,
                                    x2: 0,
                                    y2: 595 / 2,
                                    lineWidth: 0.5
                                },
                            ]
                        },
                        block
                    ],
                    pageBreak: payment.id_transport_payment !== transport_payments[transport_payments.length - 1].id_transport_payment ? 'after' : ''
                },
            )
        }
        const pdfDocGenerator = pdfMake.createPdf({
            pageSize: 'A4',
            pageOrientation: 'landscape',
            images: LOGOS,
            pageMargins: [0, 0, 0, 0],
            content: JSON.parse(JSON.stringify(contents)),

        }, null, PDF_FONTS);
        return new Promise((resolve, reject) => {
            pdfDocGenerator.getDataUrl((dataUrl) => {
                const iframe = document.querySelector("iframe");
                iframe.src = dataUrl;
                $('#PdfModal').modal('show');
                resolve(); // Resolve the Promise when the code is finished
            });
        });
    } catch (error) {
        throw error
    }
}

export { generateCoursePaymentPdf, generateTransportPaymentPdf };
