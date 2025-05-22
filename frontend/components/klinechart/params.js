const intervals = [{
		name: 'Time',
		value: '1min',
		time: 1 //天
	},
	{
		name: '1min',
		value: '1min',
		time: 1
	},
	{
		name: '5min',
		value: '5min',
		time: 5
	},
	{
		name: '30min',
		value: '30min',
		time: 30
	},
	{
		name: '1h',
		value: '60min',
		time: 60
	},
	{
		name: '1day',
		value: '1D',
		time: 360
	},
	{
		name: '1week',
		value: '1W',
		time: 1800
	},
	{
		name: '1mon',
		value: '1M',
		time: 10800
	},
]

const chartTypes = [ {
	name: '蜡烛实心',
	value: 'candle_solid'
},{
	name: '面积图',
	value: 'area'
}, {
	name: '蜡烛空心',
	value: 'candle_stroke'
}, {
	name: '蜡烛涨空心',
	value: 'candle_up_stroke'
}, {
	name: '蜡烛跌空心',
	value: 'candle_down_stroke'
}, {
	name: 'OHLC',
	value: 'ohlc'
}]

const shapes = [{
	name: '价格线',
	value: 'priceLine'
}, {
	name: '价格通道线',
	value: 'priceChannelLine'
}, {
	name: '平行直线',
	value: 'parallelStraightLine'
}, {
	name: '斐波那契回调',
	value: 'fibonacciLine'
}, {
	name: '自定义矩形',
	value: 'rect'
}, {
	name: '自定义圆',
	value: 'circle'
}, ]

const rect = {
	name: 'rect',
	totalStep: 3,
	checkEventCoordinateOnShape: ({
		dataSource,
		eventCoordinate
	}) => {
		return checkCoordinateOnSegment(dataSource[0], dataSource[1], eventCoordinate)
	},
	createShapeDataSource: ({
		coordinates
	}) => {
		if (coordinates.length === 2) {
			return [{
					type: 'line',
					isDraw: false,
					isCheck: true,
					dataSource: [
						[{
							...coordinates[0]
						}, {
							x: coordinates[1].x,
							y: coordinates[0].y
						}],
						[{
							x: coordinates[1].x,
							y: coordinates[0].y
						}, {
							...coordinates[1]
						}],
						[{
							...coordinates[1]
						}, {
							x: coordinates[0].x,
							y: coordinates[1].y
						}],
						[{
							x: coordinates[0].x,
							y: coordinates[1].y
						}, {
							...coordinates[0]
						}]
					]
				},
				{
					type: 'polygon',
					isDraw: true,
					isCheck: false,
					styles: {
						style: 'fill'
					},
					dataSource: [
						[{
								...coordinates[0]
							},
							{
								x: coordinates[1].x,
								y: coordinates[0].y
							},
							{
								...coordinates[1]
							},
							{
								x: coordinates[0].x,
								y: coordinates[1].y
							}
						]
					]
				},
				{
					type: 'polygon',
					isDraw: true,
					isCheck: false,
					dataSource: [
						[{
								...coordinates[0]
							},
							{
								x: coordinates[1].x,
								y: coordinates[0].y
							},
							{
								...coordinates[1]
							},
							{
								x: coordinates[0].x,
								y: coordinates[1].y
							}
						]
					]
				}
			]
		}
		return []
	}
}


const circle = {
	name: 'circle',
	totalStep: 3,
	checkEventCoordinateOnShape: ({
		dataSource,
		eventCoordinate
	}) => {
		const xDis = Math.abs(dataSource.x - eventCoordinate.x)
		const yDis = Math.abs(dataSource.y - eventCoordinate.y)
		const r = Math.sqrt(xDis * xDis + yDis * yDis)
		return Math.abs(r - dataSource.radius) < 3;
	},
	createShapeDataSource: ({
		coordinates
	}) => {
		if (coordinates.length === 2) {
			const xDis = Math.abs(coordinates[0].x - coordinates[1].x)
			const yDis = Math.abs(coordinates[0].y - coordinates[1].y)
			const radius = Math.sqrt(xDis * xDis + yDis * yDis)
			return [{
					type: 'arc',
					isDraw: true,
					isCheck: false,
					styles: {
						style: 'fill'
					},
					dataSource: [{
						...coordinates[0],
						radius,
						startAngle: 0,
						endAngle: Math.PI * 2
					}]
				},
				{
					type: 'arc',
					isDraw: true,
					isCheck: true,
					dataSource: [{
						...coordinates[0],
						radius,
						startAngle: 0,
						endAngle: Math.PI * 2
					}]
				}
			];
		}
		return []
	}
}

export {
	intervals,
	chartTypes,
	shapes,
	rect,
	circle
}
